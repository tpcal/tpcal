-- tpcal schema (MySQL 8.0+)
-- Optional: CREATE DATABASE tpcal CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
-- USE tpcal;

SET NAMES utf8mb4;
SET time_zone = '+00:00';

-- POLICIES_TABLE  
-- : items to rank
CREATE TABLE IF NOT EXISTS policies (

  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(255) GENERATED ALWAYS AS
    (LOWER(REPLACE(REPLACE(REPLACE(title,' ','-'),'--','-'),'''',''))) VIRTUAL,
  description TEXT NULL,
  category VARCHAR(100) NULL,
  policy_rank INT DEFAULT 0,                  -- optional seed / manual ordering
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_policies_title (title),
  KEY idx_policies_active (is_active),
  KEY idx_policies_rank (policy_rank),
  KEY idx_policies_slug (slug)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


ALTER TABLE policies ADD COLUMN url VARCHAR(500) NULL;
CREATE INDEX idx_policies_url ON policies(url);


-- USERS_TABLE
-- : anonymous or later authenticated users
CREATE TABLE IF NOT EXISTS users (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  anon_key CHAR(36) UNIQUE,                  -- UUID stored from cookie
  email VARCHAR(255) NULL UNIQUE,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

ALTER TABLE users
  
  ADD COLUMN password_hash VARCHAR(255) NULL AFTER email,
  ADD COLUMN updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at;



-- VOTES_TABLE
-- : one vote per user per policy (values 1,2,3)
CREATE TABLE IF NOT EXISTS votes (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT NULL,
  policy_id INT NOT NULL,
  value TINYINT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT chk_votes_value CHECK (value IN (1,2,3)),
  CONSTRAINT fk_votes_user FOREIGN KEY (user_id) REFERENCES users(id)
    ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT fk_votes_policy FOREIGN KEY (policy_id) REFERENCES policies(id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  UNIQUE KEY uq_vote_user_policy (user_id, policy_id),
  KEY idx_votes_policy (policy_id),
  KEY idx_votes_user (user_id),
  KEY idx_votes_policy_value (policy_id, value)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- AGGREGATES
-- : raw counts & sums per policy
CREATE OR REPLACE VIEW policy_vote_stats AS
SELECT
  p.id            AS policy_id,
  COUNT(v.id)     AS n_votes,
  COALESCE(SUM(v.value),0) AS sum_value,
  COALESCE(AVG(v.value),0) AS avg_value
FROM policies p
LEFT JOIN votes v ON v.policy_id = p.id
GROUP BY p.id;

-- RANKING VIEW
-- : Bayesian adjusted score
-- Prior: C = 2.0 (leans “Important”), m = 10 (strength)
CREATE OR REPLACE VIEW policy_rankings AS
SELECT
  p.id,
  p.title,
  p.slug,
  p.description,
  p.category,
  p.policy_rank,
  p.is_active,
  s.n_votes,
  s.sum_value,
  s.avg_value,
  -- bayes_score = (sum + m*C) / (n + m)
  ( (s.sum_value + 10 * 2.0) / NULLIF((s.n_votes + 10),0) ) AS bayes_score,
  p.created_at,
  p.updated_at
FROM policies p
JOIN policy_vote_stats s ON s.policy_id = p.id;


-- Suggested default ordering when reading:
-- SELECT * FROM policy_rankings WHERE is_active=1 ORDER BY bayes_score DESC, n_votes DESC, id ASC;
