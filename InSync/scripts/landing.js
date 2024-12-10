document.addEventListener("DOMContentLoaded", (event) => {
  const cardsChildren = document.querySelector(".cards-container").children;
  gsap.registerPlugin(ScrollTrigger);
  let delay = 0.1;
  for (let i = 0; i < cardsChildren.length; ++i) {
    const id = "#" + cardsChildren[i].id;
    gsap.fromTo(
      id,
      {
        y: 200,
        opacity: 0,
      },
      {
        scrollTrigger: { trigger: "#scroll-trigger" },
        y: 0,
        opacity: 1,
        delay: delay,
        ease: "back",
        duration: 0.3,
      }
    );
    delay += 0.2;
  }
});
