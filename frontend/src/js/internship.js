// Internship page specific functionality
document.addEventListener("DOMContentLoaded", () => {
  // Internship-specific code can be added here
  console.log("Internship page loaded");
});
ntListener("DOMContentLoaded", () => {
  const checkbox = document.querySelector("input[type='checkbox']");
  const savedTheme = localStorage.getItem("theme");

  if (savedTheme === "dark") {
    document.body.classList.add("dark");
    checkbox.checked = true;
  }

  checkbox?.addEventListener("change", () => {
    document.body.classList.toggle("dark");
    localStorage.setItem("theme", document.body.classList.contains("dark") ? "dark" : "light");
  });
});
