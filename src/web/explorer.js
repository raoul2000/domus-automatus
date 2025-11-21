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
        return { dirName: dir, dirContent: jsonResponse };
      });

  const renderBreadCrumb = (dirName) => {
    dirName.split("/").reduce(
      (acc, curDirname) => ({
        dirNames: [...acc.dirNames, curDirname],
        pathList: [
          ...acc.pathList,
          // add new path for the current dir
          `${acc.dirNames.join("/")}${
            acc.dirNames.length > 0 ? "/" : ""
          }${curDirname}`,
        ],
      }),
      { dirNames: [], pathList: [] }
    );

    const breadcrumb = document.createElement("div");
    breadcrumb.textContent = dirName;
    return breadcrumb;
  };

  const renderDir = ({ dirName, dirContent }) =>
    document.getElementById("main").replaceChildren(
      renderBreadCrumb(dirName),
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
