#!/bin/bash --

set -e

# Create database (test_db) and table (t_users)
case "${DB}" in
	mariadb|mysql)
		mysql -u root < travis/data/test_db_mysql.sql
		;;
	postgresql)
		psql < travis/data/test_db_postgresql.sql
		;;
esac