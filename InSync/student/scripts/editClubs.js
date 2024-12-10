document.addEventListener("DOMContentLoaded", function () {
  // Function to handle adding clubs
  function handleAddClub(e) {
    if (e.target.matches(".add-club")) {
      const id = e.target.getAttribute("data-id");
      const clubItemWrapper = e.target.closest('.club-item-wrapper');

      console.log(id);
      const form = document.createElement("form");
      form.method = "POST";
      form.action = "";
      const input = document.createElement("input");
      input.type = "hidden";
      input.name = "id";
      input.value = id;
      form.appendChild(input);
      document.body.appendChild(form);
      
      if (clubItemWrapper.parentElement.classList.contains('recommended-clubs-container')) {
        clubItemWrapper.remove();
      }
      form.submit();
    }
  }

  // Add event listeners to both containers
  document
    .querySelector(".add-clubs-container")
    .addEventListener("click", handleAddClub);
  
  document
    .querySelector(".recommended-clubs-container")
    .addEventListener("click", handleAddClub);

  // Handle delete functionality
  document.querySelector(".my-clubs").addEventListener("click", function (e) {
    if (e.target.matches(".delete-club")) {
      const id = e.target.getAttribute("data-id");

      fetch("editClubList.php", {
        method: "DELETE",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id: id }),
      })
        .then((response) => response.text())
        .then((data) => {
          console.log("Server response:", data);
          location.reload();
        })
        .catch((error) => {
          console.error("Error:", error);
        });
    }
  });
});