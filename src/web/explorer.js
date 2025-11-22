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
        return { dirName: dir ?? "", dirContent: jsonResponse };
      });

  const createSingleCrumb = (title, path, isLast) => {
    const result = document.createElement("li");
    if (!isLast) {
      const link = document.createElement("a");
      link.dataset.isDir = true;
      link.dataset.path = path;
      link.textContent = title;
      result.appendChild(link);
    } else {
      result.textContent = title;
    }
    return result;
  };

  const renderBreadCrumb = (dirName) => {
    const ul = document.createElement("ul");
    ul.classList.add("breadcrumbs");

    const liHome = document.createElement("li");
    const linkHome = document.createElement("a");
    linkHome.dataset.isDir = true;
    linkHome.dataset.path = "";
    linkHome.textContent = "Home";

    liHome.appendChild(linkHome);

    const liElements = dirName.split("/").reduce(
      (acc, curDirname, curIndex, arr) => ({
        dirNames: [...acc.dirNames, curDirname],
        elements: [
          ...acc.elements,
          createSingleCrumb(
            curDirname,
            `${acc.dirNames.join("/")}${
              acc.dirNames.length > 0 ? "/" : ""
            }${curDirname}`,
            curIndex === arr.length - 1
          ),
        ],
      }),
      { dirNames: [], elements: [] }
    );

    ul.append(liHome, ...liElements.elements);

    return ul;
  };

  const renderDir = ({ dirName, dirContent }) =>
    document.getElementById("main").replaceChildren(
      renderBreadCrumb(dirName),
      ...Object.entries(dirContent).map(([fileKey, fileProps]) => {
        const div = document.createElement("div");
        div.classList.add(fileProps.type === "directory" ? "dir" : "file");
        div.textContent = fileProps.name ?? "home";

        div.dataset.path = fileKey.match(/.*#[^\/]*\/(.*)/)[1];
        div.dataset.isDir = fileProps.type === "directory";
        div.dataset.name = fileProps.name;
        return div;
      })
    );

  document.addEventListener("DOMContentLoaded", (event) => {
    document.getElementById("main").addEventListener("click", (ev) => {
      if (ev.target.dataset.isDir) {
        ls(ev.target.dataset.path).then(renderDir);
      }
    });
    // initial render
    ls().then(renderDir);
  });
})();
