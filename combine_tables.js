const marks_table_rows = [...document.querySelectorAll("table.decorated.stretch")[1].querySelector("tbody").children];
const marks_table_short = marks_table_rows.filter((_, i) => i % 2 === 0);
const marks_table_desc = marks_table_rows.filter((_, i) => i % 2 === 1);

const points_table_rows = [...document.querySelectorAll("table.decorated.stretch")[2].querySelector("tbody").children];
const points_table_short = points_table_rows.filter((_, i) => i % 2 === 0);
const points_table_desc = points_table_rows.filter((_, i) => i % 2 === 1);

marks_table_short.forEach((row, i) => {
    const first_term_short = row.querySelectorAll("td")[2];
    const second_term_short = row.querySelectorAll("td")[6];

    const desc = marks_table_desc[i];

    if (first_term_short.innerHTML === "Brak ocen" && second_term_short.innerHTML === "Brak ocen") {
        first_term_short.innerHTML = points_table_short[i].querySelectorAll("td")[2].innerHTML;
        second_term_short.innerHTML = points_table_short[i].querySelectorAll("td")[5].innerHTML;
        desc.innerHTML = points_table_desc[i].innerHTML;
    }
});

const points_title = document.querySelectorAll("h3.center")[1];
points_title.parentElement.removeChild(points_title);

const points_table = document.querySelectorAll("table.decorated.stretch")[2];
points_table.parentElement.removeChild(points_table);