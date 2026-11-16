
document.addEventListener("DOMContentLoaded", function () {

    const searchInput = document.getElementById("searchInput");
    const searchScope = document.getElementById("searchScope");
    const clearButton = document.getElementById("clearSearch");
    const noResults = document.getElementById("noResults");

    const categoryTable = document.getElementById("categoryTable");
    const panels = document.querySelectorAll(".panel");

    function filterSearch() {

        const keyword = searchInput.value.toLowerCase().trim();
        const scope = searchScope.value;

        let found = false;

        // CATEGORY SEARCH
        if (categoryTable) {

            const rows = categoryTable.querySelectorAll("tbody tr");

            rows.forEach(row => {

                if (row.querySelector(".empty")) {
                    row.style.display = keyword === "" ? "" : "none";
                    return;
                }

                const cells = row.querySelectorAll("td");

                const categoryName =
                    cells[0]?.textContent.toLowerCase().trim() || "";

                const rowText =
                    row.textContent.toLowerCase();

                let match = false;

                if (keyword === "") {
                    match = true;
                }
                else if (scope === "name") {
                    match = categoryName.includes(keyword);
                }
                else if (scope === "categories") {
                    match = categoryName.includes(keyword);
                }
                else if (scope === "all") {
                    match = rowText.includes(keyword);
                }

                row.style.display = match ? "" : "none";

                if (match) found = true;

            });

            const categoryWrap = categoryTable.closest(".table-wrap");

            if (categoryWrap) {
                categoryWrap.style.display =
                    scope === "items" ? "none" : "";
            }
        }

        // ITEM SEARCH
        panels.forEach(panel => {

            const itemRows = panel.querySelectorAll("tbody tr");

            let panelFound = false;

            itemRows.forEach(row => {

                if (row.querySelector(".empty")) {
                    row.style.display = keyword === "" ? "" : "none";
                    return;
                }

                const cells = row.querySelectorAll("td");

                const itemName =
                    cells[0]?.textContent.toLowerCase().trim() || "";

                const price =
                    cells[1]?.textContent.toLowerCase() || "";

                const details =
                    cells[2]?.textContent.toLowerCase() || "";

                const addedBy =
                    cells[3]?.textContent.toLowerCase() || "";

                const updatedBy =
                    cells[4]?.textContent.toLowerCase() || "";

                const rowText =
                    row.textContent.toLowerCase();

                let match = false;

                if (keyword === "") {
                    match = true;
                }
                else if (scope === "name") {
                    // NAME ONLY
                    match = itemName.includes(keyword);
                }
                else if (scope === "items") {
                    match =
                        itemName.includes(keyword) ||
                        price.includes(keyword) ||
                        details.includes(keyword) ||
                        addedBy.includes(keyword) ||
                        updatedBy.includes(keyword);
                }
                else if (scope === "categories") {
                    match = false;
                }
                else if (scope === "all") {
                    match = rowText.includes(keyword);
                }

                row.style.display = match ? "" : "none";

                if (match) {
                    panelFound = true;
                    found = true;
                }

            });

            // SHOW OR HIDE PANEL
            if (scope === "categories") {
                panel.style.display = "none";
            }
            else {
                panel.style.display =
                    (keyword === "" || panelFound) ? "" : "none";
            }

        });

        // NO RESULTS
        if (noResults) {
            noResults.style.display =
                (keyword !== "" && !found) ? "block" : "none";
        }

    }

    searchInput.addEventListener("input", filterSearch);

    searchScope.addEventListener("change", filterSearch);

    clearButton.addEventListener("click", function () {

        searchInput.value = "";
        searchScope.value = "name";

        filterSearch();

        searchInput.focus();

    });

    filterSearch();

});
