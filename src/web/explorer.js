(function () {
  console.log("explorer loading ...");

  const ls = (dir) =>
    fetch(`/index.php?action=ls&dir=${dir ?? ""}`, {
      headers: {
        "x-authKey": "abcd",
      },
    })
      .then((response) => response.json())
      .then((jsonResponse) => {
        if (jsonResponse.error) {
          throw jsonResponse;
        }
        return jsonResponse;
      });

  const renderDir = (dirContent) =>
    document.getElementById("main").replaceChildren(
      ...Object.entries(dirContent).map(([fileKey, fileProps]) => {
        const div = document.createElement("div");
        div.classList.add(fileProps.type === "directory" ? "dir" : "file");
        div.textContent = fileProps.name;

        div.dataset.path = fileKey.match(/.*#[^\/]*\/(.*)/)[1];
        div.dataset.isDir = fileProps.type === "directory";
        div.dataset.name = fileProps.name;
        return div;
      })
    );

  document.addEventListener("DOMContentLoaded", (event) => {
    //renderDir({ "directory#domoticus/cam1": { name: "file 1" } });
    document.getElementById("main").addEventListener("click", (ev) => {
      if (ev.target.dataset.isDir) {
        ls(ev.target.dataset.path).then(renderDir);
      }
    });
    ls().then(renderDir);
  });
})();
