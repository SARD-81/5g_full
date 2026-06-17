//const express = require("express");
import express from "express";
const app = express();

app.use(express.json());

app.get("/api/health", (req, res) => {
  res.json({ status: "backend ok" });
});

app.listen(3000, "0.0.0.0", () => {
  console.log("Backend running on port 3000");
});
