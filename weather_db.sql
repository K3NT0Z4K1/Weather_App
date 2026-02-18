-- Run this in phpMyAdmin SQL tab
-- or in MySQL command line

CREATE DATABASE IF NOT EXISTS weather_db;

USE weather_db;

CREATE TABLE IF NOT EXISTS readings (
  id        INT AUTO_INCREMENT PRIMARY KEY,
  tempC     FLOAT        NOT NULL,
  tempF     FLOAT        NOT NULL,
  humidity  FLOAT        NOT NULL,
  recorded_at TIMESTAMP  DEFAULT CURRENT_TIMESTAMP
);
