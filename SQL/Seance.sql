CREATE TABLE seances (
  user_id INT NOT NULL,
  type VARCHAR(100) NOT NULL,
  duree VARCHAR(50) NOT NULL,
  date DATETIME NOT NULL,
  notes TEXT,
  FOREIGN KEY (user_id) REFERENCES users(id)
      ON DELETE CASCADE
);