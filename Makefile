# Start the development environment in detached mode
dev:
	@docker-compose up -d --build

# Stop and remove the containers
down:
	@docker-compose down

.PHONY: dev down