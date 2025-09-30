# TPCAL Website

A ranking application for Turning Point California policies, accessible at [tpcal.org](http://tpcal.org).

## Local Development Environment

This project uses Docker to create a consistent and easy-to-use development environment. It includes a PHP/Apache web server, a MySQL database, and a live-reloading service that automatically refreshes your browser when you save a file.

### Prerequisites

You must have [Docker](https://www.docker.com/products/docker-desktop/) and Docker Compose installed on your local machine.

### Running the Application

To start the entire development environment, simply run the following command in your terminal:

```shell
make dev
```

This single command will:
1.  Build the necessary Docker containers.
2.  Start the web server, database, and live-reload services.
3.  Initialize the database with the required schema and seed data on the first run.
4.  Install the required `npm` dependencies inside the live-reload container.

Once the services are running, you can access the application in your web browser at:

**[http://localhost:8082](http://localhost:8082)**

### Key Features

*   **One-Command Setup:** `make dev` is all you need to get started.
*   **Live Reloading:** When you save a change to any `.php`, `.js`, or `.css` file, your browser will automatically refresh to show the changes instantly.
*   **Consistent Environment:** Docker ensures that the application runs the same way for every developer on any machine.

### Stopping the Application

To stop all the running containers and shut down the development environment, run:

```shell
make down
```

## Manual Frontend Commands (Optional)

The development environment handles frontend dependencies automatically. However, if you need to run these commands manually, you can do so.

First, install dependencies:
```shell
npm install
```

Then, run the Tailwind CSS watcher:
```shell
npm run watch
```

