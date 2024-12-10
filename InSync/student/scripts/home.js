// document.addEventListener('DOMContentLoaded', function () {
//     const dropdowns = document.querySelectorAll('.dropdown'); // Select all dropdowns

//     dropdowns.forEach(function (dropdown) {
//         const dropdownContent = dropdown.querySelector('.dropdown-content');
//         const dropbtn = dropdown.querySelector('.dropbtn');

//         dropbtn.addEventListener('click', function (event) {
//             event.preventDefault(); // Prevent default link action
//             dropdownContent.style.display = dropdownContent.style.display === 'block' ? 'none' : 'block';
//         });

//         window.addEventListener('click', function (event) {
//             if (!dropdown.contains(event.target)) {
//                 dropdownContent.style.display = 'none';
//             }
//         });

//         // Allow default link action if the clicked element is not the dropbtn
//         dropdown.querySelectorAll('a').forEach(function (link) {
//             link.addEventListener('click', function (event) {
//                 if (event.target === dropbtn) {
//                     event.preventDefault();
//                 }
//             });
//         });
//     });
// });
