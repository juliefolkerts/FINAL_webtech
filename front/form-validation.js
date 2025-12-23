document.addEventListener("DOMContentLoaded", () => {
  const isValidEmail = (value) => {
    const trimmed = value.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(trimmed);
  };

  const showMessage = (element, message) => {
    if (!element) {
      return;
    }
    element.textContent = message;
  };

  const registerForm = document.getElementById("register-form");
  if (registerForm) {
    const emailInput = registerForm.querySelector("input[name=\"email\"]");
    const passwordInput = registerForm.querySelector("input[name=\"password\"]");
    const message = registerForm.querySelector(".validation-message");

    registerForm.addEventListener("submit", (event) => {
      const emailValue = emailInput ? emailInput.value : "";
      const passwordValue = passwordInput ? passwordInput.value : "";

      if (!isValidEmail(emailValue)) {
        event.preventDefault();
        showMessage(message, "Please enter a valid email address.");
        return;
      }

      if (passwordValue.trim().length < 6) {
        event.preventDefault();
        showMessage(message, "Password must be at least 6 characters long.");
        return;
      }

      showMessage(message, "");
    });

    [emailInput, passwordInput].forEach((input) => {
      if (!input) {
        return;
      }
      input.addEventListener("input", () => showMessage(message, ""));
    });
  }

  const emailForm = document.getElementById("update-email-form");
  if (emailForm) {
    const emailInput = emailForm.querySelector("input[name=\"email\"]");
    const message = emailForm.querySelector(".validation-message");

    emailForm.addEventListener("submit", (event) => {
      const emailValue = emailInput ? emailInput.value : "";

      if (!isValidEmail(emailValue)) {
        event.preventDefault();
        showMessage(message, "Please enter a valid email address.");
        return;
      }

      showMessage(message, "");
    });

    if (emailInput) {
      emailInput.addEventListener("input", () => showMessage(message, ""));
    }
  }

  const passwordForm = document.getElementById("update-password-form");
  if (passwordForm) {
    const newPassword = passwordForm.querySelector("input[name=\"new_password\"]");
    const confirmPassword = passwordForm.querySelector("input[name=\"confirm_password\"]");
    const message = passwordForm.querySelector(".validation-message");

    passwordForm.addEventListener("submit", (event) => {
      const newValue = newPassword ? newPassword.value : "";
      const confirmValue = confirmPassword ? confirmPassword.value : "";

      if (newValue.trim().length < 6) {
        event.preventDefault();
        showMessage(message, "New password must be at least 6 characters long.");
        return;
      }

      if (confirmValue !== newValue) {
        event.preventDefault();
        showMessage(message, "Passwords do not match.");
        return;
      }

      showMessage(message, "");
    });

    [newPassword, confirmPassword].forEach((input) => {
      if (!input) {
        return;
      }
      input.addEventListener("input", () => showMessage(message, ""));
    });
  }
});
