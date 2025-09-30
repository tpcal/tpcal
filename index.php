<?php

require __DIR__ . '/inc/bootstrap.php';
$db = pdo();

$limit = 9;
$page = max(1, (int) ($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

/**
 * We render:
 * - title, description, url
 * - n_votes, score using Bayesian adjust (C=2.0, m=10)
 * We avoid ONLY_FULL_GROUP_BY issues by pre-aggregating in a subquery.
 */
$m = 10;
$C = 2.0;

// FIX: Pre-calculate the value for the Bayesian adjustment.
// You cannot perform arithmetic with two separate placeholders in a prepared statement.
$m_times_C = $m * $C;

$total = (int) $db->query("SELECT COUNT(*) FROM policies WHERE is_active=1")->fetchColumn();

$sql = "
SELECT
  p.id
  , p.title
  , p.description
  , p.category
  , p.url
  , p.created_at
  , COALESCE(a.n_votes,0) AS n_votes
  , COALESCE(a.sum_value,0) AS sum_value
  -- FIX: Use a single placeholder for the pre-calculated value.
  , ((COALESCE(a.sum_value,0) + :m_times_C) / (COALESCE(a.n_votes,0) + :m)) AS bayes_score

FROM policies p
LEFT JOIN (
SELECT policy_id, COUNT(*) AS n_votes, SUM(value) AS sum_value
FROM votes
GROUP BY policy_id
) a ON a.policy_id = p.id

WHERE p.is_active=1
ORDER BY bayes_score DESC, n_votes DESC, p.id ASC
LIMIT :limit OFFSET :offset

";
$st = $db->prepare($sql);

// FIX: Bind the new pre-calculated value and remove the old :C binding.
$st->bindValue(':m_times_C', $m_times_C);
$st->bindValue(':m', $m, PDO::PARAM_INT);
$st->bindValue(':limit', $limit, PDO::PARAM_INT);
$st->bindValue(':offset', $offset, PDO::PARAM_INT);
$st->execute(); // This was the line (50) causing the error
$policies = $st->fetchAll();

$lastPage = max(1, (int) ceil($total / $limit));

?>

<?php require __DIR__ . '/inc/header.php'; ?>


<!-- Hero Section -->
<div class="bg-softBlue py-4">
    <div class="text-center">
        <h1 class="text-3xl font-semibold tracking-tight sm:text-7xl text-white">
            Rank California Policies 🇺🇸
        </h1>
        <h2 class="text-base/7 font-semibold">
            <span class="px-3 py-2 text-sm text-white">Page <?= $page ?> of <?= $lastPage ?></span>
        </h2>
    </div>
</div>



<!-- POLICY CARDS -->

<section id="policy-cards" class="py-4">

    <!-- card container -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 p-6">

        <!-- card -->
        <?php foreach ($policies as $p): ?>

            <div class="px-3 mb-6 w-full flex flex-col py-6 space-y-4 text-center rounded-lg shadow-lg"
                data-policy-id="<?= (int) $p['id'] ?>">

                <h3 class="text-lg font-bold"><?= htmlspecialchars($p['title']) ?></h3>

                <?php if (!empty($p['category'])): ?>
                    <p class="mt-1 text-xs uppercase tracking-wide text-gray-400">
                        <?= htmlspecialchars($p['category']) ?>
                    </p>
                <?php endif; ?>

                <p class="mt-3 text-sm text-gray-600"><?= htmlspecialchars($p['description'] ?? '') ?></p>

                <?php if (!empty($p['url'])): ?>
                    <p class="mt-3 text-sm">
                        <a class="text-blue-600 hover:underline" href="<?= htmlspecialchars($p['url']) ?>" target="_blank"
                            rel="noopener">
                            Learn more
                        </a>
                    </p>
                <?php endif; ?>

                <div class="mt-6 bg-dots bg-repeat-x px-6 pt-6 capitalize">
                    <div class="flex justify-center gap-2">
                        <button class="vote px-3 py-2 rounded border border-gray-300 hover:bg-gray-50" data-v="3">Very
                            Important</button>
                        <button class="vote px-3 py-2 rounded border border-gray-300 hover:bg-gray-50"
                            data-v="2">Important</button>
                        <button class="vote px-3 py-2 rounded border border-gray-300 hover:bg-gray-50" data-v="1">Not
                            Important</button>
                    </div>
                    <p class="mt-3 text-xs text-gray-500 text-center">
                        Score: <span class="score"><?= number_format((float) $p['bayes_score'], 2) ?></span>
                        · Votes: <span class="n"><?= (int) $p['n_votes'] ?></span>
                    </p>
                </div>
            </div>

        <?php endforeach; ?>

    </div>

    <!-- Pagination -->
    <div class="mt-10 flex items-center justify-center gap-2">
        <?php $prev = max(1, $page - 1);
        $next = min($lastPage, $page + 1); ?>
        <a href="?page=1"
            class="px-3 py-2 text-sm rounded border <?= $page == 1 ? 'opacity-50 pointer-events-none' : '' ?>">«
            First
        </a>
        <a href="?page=<?= $prev ?>"
            class="px-3 py-2 text-sm rounded border <?= $page == 1 ? 'opacity-50 pointer-events-none' : '' ?>">‹
            Prev
        </a>
        <span class="px-3 py-2 text-sm text-gray-600">Page <?= $page ?> of <?= $lastPage ?></span>

        <a href="?page=<?= $next ?>"
            class="px-3 py-2 text-sm rounded border <?= $page == $lastPage ? 'opacity-50 pointer-events-none' : '' ?>">Next
            ›
        </a>
        <a href="?page=<?= $lastPage ?>"
            class="px-3 py-2 text-sm rounded border <?= $page == $lastPage ? 'opacity-50 pointer-events-none' : '' ?>">Last
            »
        </a>
    </div>
</section>

<br>

<script>

    const CSRF = "<?= htmlspecialchars(csrf_token()) ?>";

    document.querySelectorAll('.vote').forEach(b => {
        b.addEventListener('click', async () => {
            const card = b.closest('[data-policy-id]');
            const policy_id = +card.dataset.policyId;
            const value = +b.dataset.v;
            try {
                const r = await fetch('/api_vote.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': CSRF },
                    body: JSON.stringify({ policy_id, value })
                });
                const j = await r.json();
                if (j.ok) {
                    card.querySelector('.score').textContent = (j.score).toFixed(2);
                    card.querySelector('.n').textContent = j.n;
                } else {
                    alert('Vote failed');
                }
            } catch (e) { alert('Network error'); }
        });
    });

</script>

<?php include __DIR__ . '/inc/footer.php'; ?>