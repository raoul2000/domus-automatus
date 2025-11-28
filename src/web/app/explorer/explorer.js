(function () {
  console.log("explorer loading ...");

  const ls = (dir) =>
    fetch(`../../api/index.php?action=ls&dir=${dir ?? ""}`, {
      headers: {
        "x-authKey": key,
      },
    })
      .then((response) => response.json())
      .then((jsonResponse) => {
        if (jsonResponse.error) {
          throw jsonResponse;
        }
        return { dirName: dir ?? "", dirContent: jsonResponse };
      });

  let loading = false;
  const loadingInProgress = (inProgress) => {
    loading = inProgress;
    const loadingElement = document.getElementById("loading");
    if (!loadingElement) {
      return;
    } else if (inProgress) {
      loadingElement.classList.remove("hidden");
    } else {
      loadingElement.classList.add("hidden");
    }
    return Promise.resolve(true);
  };

  const createSingleCrumb = (title, path, isLast) => {
    const result = document.createElement("li");
    if (!isLast) {
      const link = document.createElement("a");
      link.setAttribute("href", "");
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
    linkHome.setAttribute("href", "");
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
        const isDir = fileProps.type === "directory";

        let icon = "";
        if (isDir) {
          icon = "📁";
        } else {
          const groups = fileProps.name.match(/.*\.([^\.]*)/);
          const ext = groups ? groups[1] : "";
          switch (ext) {
            case "mp4":
              icon = "🎥";
              break;
            case "jpg":
              icon = "🖼️";
              break;
            default:
              icon = "📄";
          }
        }
        const div = document.createElement("div");
        div.classList.add(isDir ? "dir" : "file");
        div.textContent = `${icon} ` + fileProps.name ?? "home";
        div.dataset.path = fileKey.match(/.*#[^\/]*\/(.*)/)[1];
        div.dataset.isDir = isDir;
        div.dataset.name = fileProps.name;
        return div;
      })
    );

  const updateMainView = (pathToBrowse) =>
    loadingInProgress(true)
      .then(() => ls(pathToBrowse))
      .then(renderDir)
      .finally(() => loadingInProgress(false));

  document.addEventListener("DOMContentLoaded", (event) => {
    document.getElementById("main").addEventListener("click", (ev) => {
      ev.preventDefault();
      ev.stopPropagation();
      if (!loading) {
        if (ev.target.dataset.isDir === "true") {
          updateMainView(ev.target.dataset.path);
        } else if (ev.target.dataset.isDir === "false") {
          const fileUrl = `http://manu34.free.fr/domoticus/${ev.target.dataset.path}`;
          console.log(`opening url ${fileUrl}`);
          window.open(fileUrl);
        }
      }
    });

    // initial render
    updateMainView();
  });
})();
