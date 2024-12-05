### Application Deployment and Local Setup Documentation

For a quick demo, the application is deployed in the cloud:
**Demo URL:** [http://134.122.123.154](http://134.122.123.154)

If you'd like to test the application locally, follow the instructions below.

---

### Prerequisites

Ensure the following dependencies are installed:

- **Make**: Command-line tool for managing build automation
- **Docker**: Containerization platform
- **nvm**: Node Version Manager for managing Node.js versions
- **yarn**: JavaScript package manager

> **Note:** The setup works best on a **macOS** system.

---

### Steps to Set Up the Application Locally

#### 1. **Create the `.env` File**
Set up the environment variables by copying the example configuration file:

```bash
cp .env.example .env
```

Update the `.env` file with your configuration values as needed.

---

#### 2. **Set Up Docker Containers**
Start the necessary containers for the application:

```bash
make up
```

---

#### 3. **Run Migrations and Seed the Database**
Run the database migrations and seed data:

```bash
make refresh-migration-with-seeding
```

---

#### 4. **Install Node.js and Yarn Dependencies**

- **Install Node.js with nvm:**
  Use `nvm` to install and use the specific Node.js version:
   ```bash
   nvm install 18.17.0
   nvm use
   npm install -g yarn@1.22.22 --force
   ```

- **Install dependencies with Yarn:**
  Install the required JavaScript dependencies:
   ```bash
   yarn install
   ```

---

#### 5. **Start the Development Server**
Start the front-end development server with hot-reloading:

```bash
yarn dev
```

---

#### 6. **Serve the Laravel Application**
Run the Laravel development server (optional):

```bash
make serve
```

Access the Laravel application structure at [http://localhost:8000](http://localhost:8000).

---

#### 7. **Access the Application**
Once the development server is running, open your browser and go to:

```
http://localhost:8000
```

---

#### 8. **Run Tests**
Ensure the application works as expected by running all tests:

```bash
make test
```

---

#### 9. **Stop Docker Containers**
When you are done working on the project, stop all running containers:

```bash
make stop
```

---

### Using RabbitMQ for Background Tasks

#### 1. **Access the RabbitMQ Admin Panel**
Open the RabbitMQ admin panel in your browser:

```
http://localhost:15672
```

#### 2. **Login Credentials**
- **Username:** `guest`
- **Password:** `guest`

Use RabbitMQ to handle background tasks effectively.

---
