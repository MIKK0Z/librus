const legend_1 = document.querySelectorAll(".legend.left.stretch")[0];
const legend_1_ul = document.querySelectorAll(".legend.left.stretch")[0].children[1].children[0];
const legend_1_children = [...legend_1.children[1].children[0].children];

const legend_2 = document.querySelectorAll(".legend.left.stretch")[1];
const legend_2_children = [...legend_2.children[1].children[0].children];

const legend_tags = new Set();
legend_1_children.forEach((el) => {
    legend_tags.add(el.innerHTML);
});
legend_2_children.forEach((el) => {
    legend_tags.add(el.innerHTML);
});

legend_1_ul.innerHTML = "";
legend_tags.forEach((tag) => {
    const li = document.createElement("li");
    li.classList.add("double");
    li.classList.add("big");
    li.innerHTML = tag;
    legend_1_ul.appendChild(li);
});

// wyn II sie nie przenosi, a wyn I traci <p>

legend_2.parentElement.removeChild(legend_2);