document.addEventListener("DOMContentLoaded", function (e) {
  const searchInput = document.getElementById("searchInput");
  const customSelect = document.getElementById("customSelect");
  const selectedValue = document.getElementById("selectedValue");
  const originalSelect = document.getElementById("originalSelect");
  const selectOptions = document.querySelectorAll(".select-option");
  const selectedItemsContainer = document.getElementById("selectedItems");
  const form = document.querySelector("form");
  const edit = document.querySelector(".edit-btn");
  const cancel = document.querySelector(".cancel-btn");
  const normalFields = document.querySelector(".normal-fields");
  const editFields = document.querySelector(".edit-fields");
  const submit = document.getElementById("submit");
  // Initialize the set with pre-selected items
  let selectedItems = new Set();

  // Function to update the hidden select element
  function updateHiddenSelect() {
    // Clear existing options
    originalSelect.innerHTML = "";

    // Add selected items as options
    selectedItems.forEach((item) => {
      const option = document.createElement("option");
      option.value = item;
      option.selected = true;
      originalSelect.appendChild(option);
    });

    // Important: If no items are selected, add a dummy option to trigger POST
    if (selectedItems.size === 0) {
      const option = document.createElement("option");
      option.value = "";
      option.selected = true;
      originalSelect.appendChild(option);
    }
  }

  // Function to render selected items
  function renderSelectedItems() {
    selectedItemsContainer.innerHTML = "";
    selectedItems.forEach((item) => {
      const tag = document.createElement("div");
      tag.className = "selected-tag";
      tag.innerHTML = `
                ${item}
                <span class="remove-tag hidden" data-value="${item}">×</span>
            `;
      selectedItemsContainer.appendChild(tag);
    });

    if (selectedItems.size === 0) {
      selectedValue.textContent = "Select items";
    } else {
      selectedValue.textContent = `${selectedItems.size} item${
        selectedItems.size === 1 ? "" : "s"
      } selected`;
    }

    // Update hidden select whenever we render
    updateHiddenSelect();
    editEvent();
  }

  // Populate the selected items from server-rendered options
  const preSelectedOptions = document.querySelectorAll(
    "#originalSelect option[selected]"
  );
  preSelectedOptions.forEach((option) => {
    const value = option.value;
    selectedItems.add(value);
  });

  // Render the pre-selected items initially
  renderSelectedItems();

  // Show/hide custom select on click
  selectedValue.addEventListener("click", function () {
    const isVisible = customSelect.style.display === "block";
    customSelect.style.display = isVisible ? "none" : "block";
    searchInput.style.display = isVisible ? "none" : "block";

    if (!isVisible) {
      searchInput.focus();
    } else {
      searchInput.value = "";
      selectOptions.forEach((option) => {
        option.style.display = "block";
      });
    }
  });

  // Handle search input
  searchInput.addEventListener("input", function (e) {
    const searchText = e.target.value.toLowerCase();
    selectOptions.forEach((option) => {
      const text = option.textContent.toLowerCase();
      option.style.display = text.includes(searchText) ? "block" : "none";
    });
  });

  // Handle option selection
  selectOptions.forEach((option) => {
    option.addEventListener("click", function () {
      const value = this.dataset.value;

      if (selectedItems.has(value)) {
        selectedItems.delete(value);
        this.classList.remove("selected");
      } else {
        selectedItems.add(value);
        this.classList.add("selected");
      }

      renderSelectedItems();
      searchInput.focus();
    });
  });

  // Handle removing tags
  selectedItemsContainer.addEventListener("click", function (e) {
    if (e.target.classList.contains("remove-tag")) {
      const value = e.target.dataset.value;
      selectedItems.delete(value);

      // Update the visual state of the option in the dropdown
      selectOptions.forEach((option) => {
        if (option.dataset.value === value) {
          option.classList.remove("selected");
        }
      });

      // Important: Render and update hidden select immediately
      renderSelectedItems();
    }
  });

  // Close dropdown when clicking outside
  document.addEventListener("click", function (e) {
    if (!e.target.closest(".select-container")) {
      customSelect.style.display = "none";
      searchInput.style.display = "none";
      searchInput.value = "";
      selectOptions.forEach((option) => {
        option.style.display = "block";
      });
    }
  });

  // Handle form submission
  form.addEventListener("submit", function (e) {
    // Update hidden select before submission
    updateHiddenSelect();
  });

  function editEvent() {
    edit.addEventListener("click", function (e) {
      selectedValue.classList.remove("hidden");
      const allRemove = document.querySelectorAll(".remove-tag");
      allRemove.forEach((element) => element.classList.remove("hidden"));
      cancel.classList.remove("hidden");
      edit.classList.add("hidden");
      editFields.classList.remove("hidden");
      normalFields.classList.add("hidden");
      submit.classList.remove("hidden");
      cancelEdit();
      document
        .querySelector("#image")
        .addEventListener("change", function (event) {
          console.log("ok");
          let output = document.getElementById("output");
          output.src = URL.createObjectURL(event.target.files[0]);

          output.onload = function () {
            URL.revokeObjectURL(output.src);
          };
        });
    });
  }

  function cancelEdit() {
    cancel.addEventListener("click", function (e) {
      window.location.reload();
    });
  }
  document.querySelector("#image").addEventListener("change", function (event) {
    console.log("ok");
    let output = document.getElementById("output");
    output.src = URL.createObjectURL(event.target.files[0]);

    output.onload = function () {
      URL.revokeObjectURL(output.src);
    };
  });
});
