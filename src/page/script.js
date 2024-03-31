document.addEventListener(
    "DOMContentLoaded",
    () => {
        var itemsTable = document.getElementById("items-table");
        fetch("127.0.0.1:8000/data").then(
        (response) => response.json()
        ).then((json) => console.log(json));
    }
)