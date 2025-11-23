CREATE DATABASE IF NOT EXISTS tracklin
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

CREATE USER IF NOT EXISTS 'tracklin_user'@'localhost'
IDENTIFIED BY 'Shes_electric';

GRANT ALL PRIVILEGES ON tracklin.* TO 'tracklin_user'@'localhost';

FLUSH PRIVILEGES;
