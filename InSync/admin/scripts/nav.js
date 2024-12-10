document.addEventListener("DOMContentLoaded", (event) => {
  const buttons = document.querySelectorAll(".nav-button");
  const hamburger = document.querySelector("#open-nav");
  const nav = document.querySelector("#nav-bar");
  const width = nav.offsetWidth;
  let isOpen = true;

  hamburger.addEventListener("click", function (e) {
    if (isOpen) {
      gsap.fromTo(
        "#nav-bar",
        { x: 0 },
        { display: "none", x: -400, duration: 0.35, width: 0 }
      );
      isOpen = false;
    } else {
      nav.style.display = "block";
      gsap.fromTo(
        "#nav-bar",
        { x: -500 },
        { x: 0, duration: 0.3, width: width }
      );
      gsap.fromTo(
        ".nav-links > *",
        { opacity: 0, x: -300 },
        {
          delay: 0.1,
          stagger: 0.1,
          duration: 0.25,
          x: 0,
          opacity: 1,
          ease: "back",
        }
      );
      isOpen = true;
    }
  });

  buttons.forEach((button) => {
    button.addEventListener("click", function (e) {
      const isOpen = button.getAttribute("data-open");
      const listId = e.currentTarget.id + "-list";

      if (isOpen === "false") {
        let delayTime = 0;
        console.log(isOpen);
        gsap.fromTo(
          `#${listId}`,
          { y: -20, opacity: 0 },
          { display: "block", opacity: 1, y: 0, ease: "back", duration: 0.5 }
        );
        const listChildren = document.querySelector(`#${listId}`).children;
        for (let i = 0; i < listChildren.length; i++) {
          console.log(listChildren);
          gsap.fromTo(
            listChildren[i],
            { y: -30, opacity: 0 },
            { y: 0, opacity: 1, delay: delayTime }
          );
          delayTime += 0.15;
        }
        button.querySelector(".dropdown-icon").style.rotate = "180deg";
        button.setAttribute("data-open", "true");
      } else {
        button.querySelector(".dropdown-icon").style.rotate = "0deg";
        gsap.fromTo(
          `#${listId}`,
          { opacity: 1, y: 0 },
          {
            display: "none",
            opacity: 0,
            autoAlpha: 1,
            duration: 0.15,
          }
        );

        button.setAttribute("data-open", "false");
      }
    });
  });
});
