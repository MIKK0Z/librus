const marks_table_rows = document.querySelectorAll("table.decorated.stretch")[1].querySelector("tbody").children;
const points_table_rows = document.querySelectorAll("table.decorated.stretch")[2].querySelector("tbody").children;

[...marks_table_rows].filter((_, i) => i % 2 === 0).forEach((row, j) => {
    const first_term = row.querySelectorAll("td")[2];
    if (first_term.innerHTML === "Brak ocen") {
        first_term.innerHTML = points_table_rows[(2 * j)].querySelectorAll("td")[2].innerHTML;
    }

    const second_term = row.querySelectorAll("td")[6];
    if (second_term.innerHTML === "Brak ocen") {
        second_term.innerHTML = points_table_rows[(2 * j)].querySelectorAll("td")[5].innerHTML;
    }

    // TODO dodać te oceny do rozwijanej tabelki
});


