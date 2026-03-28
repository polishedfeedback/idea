## Postgres basics and learning

### The basics
- `postgres` - this is the server that runs in the background (like a daemon)
- `psql` - this is the client that we can use to interact with postgres from the terminal / commandline
- `createdb {DATABASE_NAME}` - can run this even outside psql which creates a postgres database
- `psql postgres` - connects to default postgres database and puts you inside REPL mode

### Inside the REPL
- Once you're inside the REPL, you can run these commands
  - `CREATE DATABASE {DATABASE_NAME};` - creates a database
  - `\l` - to list out the databases
  - `\c {DATABASE_NAME}` - to connect to your database
  - `\q` - to quit postgres

### Connecting to the database directly
- `psql {DATABASE_NAME}` - connect directly to the database without going to the REPL
- `psql -U {DATABASE_OWNER} -d {DATABASE_NAME} -h {HOST} -p {PORT_NAME}`

### Setting passwords
- `psql {DATABASE_USER}` - `\password {USER_NAME}`
- `psql -U {USER_NAME} -d {DATABASE_NAME} -W` - this enforces to ask password

### Postgres config file
- `pg_hba.conf` - you can also run `psql postgres -c "SHOW_HBA_FILE";`

### See the tables inside database inside psql when you're connected to the database
- `\dt` - see all the tables. `Did not find any realtions` suggest that you have no tables inside your connected database
- - `\d {TABLE_NAME}` - shows the columns inside the table
