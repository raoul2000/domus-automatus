(function () {
  console.log("explorer loading ...");

  const formatBytes = (bytes, decimals = 2) => {
    if (!+bytes) return "0 Bytes";

    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = [
      "Bytes",
      "KiB",
      "MiB",
      "GiB",
      "TiB",
      "PiB",
      "EiB",
      "ZiB",
      "YiB",
    ];

    const i = Math.floor(Math.log(bytes) / Math.log(k));

    return `${parseFloat((bytes / Math.pow(k, i)).toFixed(dm))} ${sizes[i]}`;
  };

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
    if (dirName.startsWith("/")) {
      dirName = dirName.substr(1);
    }
    const ul = document.createElement("ul");
    ul.classList.add("breadcrumbs");

    const liHome = document.createElement("li");
    const linkHome = document.createElement("a");
    linkHome.setAttribute("href", "");
    linkHome.dataset.isDir = true;
    linkHome.dataset.path = "";
    linkHome.textContent = "Root";

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
            curIndex === arr.length - 1,
          ),
        ],
      }),
      { dirNames: [], elements: [] },
    );

    ul.append(liHome, ...liElements.elements);

    return ul;
  };

  const renderDir = ({ dirName, dirContent }) => {
    console.log(dirName, dirContent);
    if (dirContent.error) {
      console.error(dirContent.error);
    } else {
      document.getElementById("main").replaceChildren(
        renderBreadCrumb(dirName),
        ...dirContent.content.map((fileProps) => {
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
          div.classList.add("row", isDir ? "dir" : "file");

          div.dataset.path = `${dirName}/${fileProps.name}`;
          div.dataset.isDir = isDir;
          div.dataset.name = fileProps.name;
          div.dataset.size = fileProps.size;

          const iconCol = document.createElement("div");
          iconCol.textContent = icon;
          iconCol.classList.add("icon");

          const timeCol = document.createElement("div");
          timeCol.classList.add("time");
          timeCol.textContent = fileProps.time;

          const filenameCol = document.createElement("span");
          filenameCol.classList.add("filename");
          filenameCol.textContent = fileProps.name;

          const fileSize = document.createElement("span");
          fileSize.classList.add("filesize");
          fileSize.textContent = formatBytes(fileProps.size);
          div.replaceChildren(iconCol, timeCol, filenameCol, fileSize);
          return div;
        }),
      );
    }
  };

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
        const linkElement = ev.target.closest("[data-path]");
        if (linkElement.dataset.isDir === "true") {
          updateMainView(linkElement.dataset.path);
        } else if (linkElement.dataset.isDir === "false") {
          const fileUrl = `http://manu34.free.fr/domoticus/${linkElement.dataset.path}`;
          console.log(`opening url ${fileUrl}`);
          window.open(fileUrl);
        }
      }
    });

    // initial render
    updateMainView();
  });
})();
