document.addEventListener("DOMContentLoaded", function () {
  console.log("ok");
  let regex = `!@#$%^&*()-_+=\[\]{}|;:'\",.<>/?`;

  let passwordBox = document.querySelector("#password");
  let rePassword = document.querySelector("#repassword");
  passwordBox.addEventListener("click", function () {
    document.querySelector("#password-checks").style.display = "block";
  });
  passwordBox.addEventListener("keyup", function () {
    let value = passwordBox.value;
    document.querySelector("#upper").className = "wrong";
    document.querySelector("#lower").className = "wrong";
    document.querySelector("#number").className = "wrong";
    document.querySelector("#special").className = "wrong";
    document.querySelector("#match").className = "wrong";
    let matchingValue = rePassword.value;
    if (value === matchingValue && value !== "") {
      document.querySelector("#match").className = "check";
    }
    value.split("").forEach((element) => {
      if ("0123456789".includes(element)) {
        document.querySelector("#number").className = "check";
      } else if (regex.includes(element)) {
        document.querySelector("#special").className = "check";
      } else if (element.toUpperCase() === element) {
        document.querySelector("#upper").className = "check";
      } else if (element.toLowerCase() === element) {
        document.querySelector("#lower").className = "check";
      }
    });
  });

  rePassword.addEventListener("keyup", function () {
    let value = passwordBox.value;
    let matchingValue = rePassword.value;
    document.querySelector("#match").className = "wrong";
    if (value === matchingValue && value !== "") {
      document.querySelector("#match").className = "check";
    }
  });
  let button = document.querySelector(".submit");
  document.querySelector("form").addEventListener("keyup", function () {
    console.log("key");
    if (
      document.querySelector("#first-name").value !== "" &&
      document.querySelector("#last-name").value !== "" &&
      document.querySelector("#email").value !== "" &&
      document.querySelector("#major").value !== "" &&
      document.querySelector("#dob").value !== "" &&
      document.querySelectorAll(".wrong").length === 0
    ) {
      button.disabled = false;
    } else {
      button.disabled = true;
    }
  });
});
