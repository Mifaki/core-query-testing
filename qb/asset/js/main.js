// Remove all fetch-based functions and replace with form submission approach
// This approach works with your existing PHP controller structure

// Store test results for potential comparison
const testResults = {
  core: {},
  laravel: {},
};

// Function to handle form submissions with loading states
function submitScenarioTest(scenario) {
  const buttons = document.querySelectorAll(".test-button");
  const resultDiv = document.getElementById("result");

  // Disable all buttons to prevent multiple submissions
  buttons.forEach((btn) => (btn.disabled = true));

  // Show loading state
  if (resultDiv) {
    resultDiv.style.display = "block";
    resultDiv.className = "result";
    resultDiv.innerHTML =
      '<div class="loading">Running CORE Framework analysis...</div>';
  }

  // Create and submit form
  const form = document.createElement("form");
  form.method = "POST";
  form.action = ""; // Submit to current page

  const scenarioInput = document.createElement("input");
  scenarioInput.type = "hidden";
  scenarioInput.name = "scenario";
  scenarioInput.value = scenario;

  form.appendChild(scenarioInput);
  document.body.appendChild(form);
  form.submit();
}

// Function to add click handlers to buttons
function initializeScenarioButtons() {
  const buttons = document.querySelectorAll(".test-button");

  buttons.forEach((button) => {
    button.addEventListener("click", function (e) {
      e.preventDefault();
      const scenario = this.getAttribute("data-scenario") || this.value;
      submitScenarioTest(scenario);
    });
  });
}

// Initialize when DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
  initializeScenarioButtons();

  // If there are results shown, re-enable buttons
  const resultDiv = document.getElementById("result");
  if (resultDiv && resultDiv.style.display !== "none") {
    const buttons = document.querySelectorAll(".test-button");
    buttons.forEach((btn) => (btn.disabled = false));
  }
});

// Optional: Add smooth scrolling to results
function scrollToResults() {
  const resultDiv = document.getElementById("result");
  if (resultDiv && resultDiv.style.display !== "none") {
    resultDiv.scrollIntoView({
      behavior: "smooth",
      block: "start",
    });
  }
}

// Call scroll to results if results are present
document.addEventListener("DOMContentLoaded", function () {
  setTimeout(scrollToResults, 100); // Small delay to ensure rendering is complete
});

// Function to handle comparison between frameworks (if you later add Laravel comparison)
function prepareComparisonData() {
  // This function can be expanded later when you add Laravel comparison
  // For now, it just stores CORE results for future comparison
  const resultElement = document.querySelector(".xhprof-section");
  if (resultElement) {
    // Extract scenario from page
    const scenarioHeader = document.querySelector("#result h3");
    if (scenarioHeader) {
      const scenarioText = scenarioHeader.textContent;
      // Store results for potential future comparison
      console.log("CORE Framework results stored for:", scenarioText);
    }
  }
}

// Initialize comparison data storage
document.addEventListener("DOMContentLoaded", function () {
  prepareComparisonData();
});

// Optional: Add keyboard shortcuts for testing scenarios
document.addEventListener("keydown", function (e) {
  // Only trigger if not typing in an input field
  if (e.target.tagName !== "INPUT" && e.target.tagName !== "TEXTAREA") {
    if (e.key >= "1" && e.key <= "5") {
      const scenario = `scenario${e.key}`;
      const button = document.querySelector(`[value="${scenario}"]`);
      if (button && !button.disabled) {
        e.preventDefault();
        submitScenarioTest(scenario);
      }
    }
  }
});

// Add visual feedback for button interactions
function addButtonFeedback() {
  const buttons = document.querySelectorAll(".test-button");

  buttons.forEach((button) => {
    button.addEventListener("mouseenter", function () {
      if (!this.disabled) {
        this.style.transform = "translateY(-2px)";
        this.style.boxShadow = "0 4px 8px rgba(0,0,0,0.15)";
      }
    });

    button.addEventListener("mouseleave", function () {
      this.style.transform = "";
      this.style.boxShadow = "";
    });

    button.addEventListener("mousedown", function () {
      if (!this.disabled) {
        this.style.transform = "translateY(0px)";
      }
    });
  });
}

// Initialize button feedback
document.addEventListener("DOMContentLoaded", function () {
  addButtonFeedback();
});

// Function to export results (optional feature)
function exportResults() {
  const resultDiv = document.getElementById("result");
  if (resultDiv && resultDiv.innerHTML) {
    const resultsText = resultDiv.innerText;
    const blob = new Blob([resultsText], { type: "text/plain" });
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = `core-framework-performance-${Date.now()}.txt`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
  }
}

// Add export button functionality if export button exists
document.addEventListener("DOMContentLoaded", function () {
  const exportBtn = document.getElementById("export-results");
  if (exportBtn) {
    exportBtn.addEventListener("click", exportResults);
  }
});
