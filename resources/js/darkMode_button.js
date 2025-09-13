const btn = document.getElementById("theme-toggle");
const checkBox = document.getElementById("check-dark-mode");

checkBox.addEventListener("change", () => {
  if (document.documentElement.classList.contains("dark")) {
    document.documentElement.classList.remove("dark");
    localStorage.theme = "light";
    console.log(checkBox.parentNode.querySelector(".toggler"));
    checkBox.parentNode.querySelector(".toggler").classList.add("translate-x-8");
    
} else {
    document.documentElement.classList.add("dark");
    localStorage.theme = "dark";
    checkBox.parentNode.querySelector(".toggler").classList.remove("translate-x-8");

  }
});

// On page load, apply saved theme or default to system
if (
  localStorage.theme === "dark" ||
  (!("theme" in localStorage) && window.matchMedia("(prefers-color-scheme: dark)").matches)
) {
  document.documentElement.classList.add("dark");
} else {
  document.documentElement.classList.remove("dark");
}
