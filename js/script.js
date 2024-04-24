document.addEventListener('DOMContentLoaded', function() {
    var searchBox = document.getElementById('course-search');
    var filterYear = document.getElementById('filter_year');
    var filterSemester = document.getElementById('filter_semester');
    var tables = document.querySelectorAll('.course-table .table-container');
    var searchTimer;
    

    function toggleSelects(disabled) {
        filterYear.disabled = disabled;
        filterSemester.disabled = disabled;
    }

    filterYear.addEventListener('change', filterCourses);
    filterSemester.addEventListener('change', filterCourses);

    function filterCourses() {
        var selectedYear = filterYear.value;
        var selectedSemester = filterSemester.value;

        tables.forEach(function(table) {
            var semesterTitle = table.querySelector('h2').textContent.trim();
            var year = semesterTitle.split(" ")[0];
            var semester = semesterTitle.split(" ")[2];

            var tableVisible = true;

            if ((selectedYear !== 'all' && selectedYear !== year) ||
                (selectedSemester !== 'all' && selectedSemester !== semester)) {
                tableVisible = false;
            }

            table.style.display = tableVisible ? '' : 'none';
        });

        var validTableSelected = Array.from(tables).some(function(table) {
            return table.style.display !== 'none';
        });

        if (!validTableSelected) {
            alert('No courses found for the selected year and semester.');
        }
    }

    searchBox.addEventListener('input', function() {
        clearTimeout(searchTimer);

        searchTimer = setTimeout(function() {
            var searchValue = searchBox.value.trim().toLowerCase();
            var found = false;

            tables.forEach(function(table) {
                var semesterTitle = table.querySelector('h2').textContent.trim();
                var year = semesterTitle.split(" ")[0];
                var semester = semesterTitle.split(" ")[2];

                if ((filterYear.value !== 'all' && filterYear.value !== year) ||
                    (filterSemester.value !== 'all' && filterSemester.value !== semester)) {
                    return; // Skip searching if the table is not visible
                }

                var rows = table.querySelectorAll('table tbody tr');

                rows.forEach(function(row, rowIndex) {
                    if (rowIndex !== 0) {
                        var cells = row.querySelectorAll('td');
                        var rowVisible = false;

                        cells.forEach(function(cell, cellIndex) {
                            var cellText = cell.textContent.trim().toLowerCase();
                            if (cellText.includes(searchValue)) {
                                rowVisible = true;
                                found = true;
                            }
                        });

                        row.style.display = rowVisible ? '' : 'none';
                    } else {
                        row.style.display = '';
                    }
                });

                var tableVisible = Array.from(rows).some(function(row, rowIndex) {
                    return rowIndex !== 0 && row.style.display !== 'none';
                });

                table.style.display = tableVisible ? '' : 'none';
            });

            if (!found && searchValue !== '') {
                alert('No matching results found.');
            }

            if (searchValue === '') {
                filterCourses();
                
                toggleSelects(false);
            } else {
               
                toggleSelects(true);
            }
        }, 600);
    });
});
