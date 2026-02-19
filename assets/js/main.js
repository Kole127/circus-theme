(function () {
  const $ = (sel, root = document) => root.querySelector(sel);

  const jokesRoot = document.querySelector("[data-jokes]");
  const btnGet = document.querySelector(".js-get-jokes");

  console.log(jokesRoot);
  console.log(btnGet);

  const filtersEl = $("[data-jokes-filters]", jokesRoot);
  const listEl = $("[data-jokes-list]", jokesRoot);
  const moreEl = $("[data-jokes-more]", jokesRoot);

  console.log(filtersEl);
  console.log(listEl);
  console.log(moreEl);

  let jokes = [];
  let types = [];
  let activeType = "";
  let isLoading = false;

  const btnGetLabel = btnGet.textContent;

  function extractTypes(jokesArr) {
    const extracted = jokesArr
      .map((j) => (j && j.type ? String(j.type) : ""))
      .filter(Boolean)
      .sort(); 

    console.log("Extracted types:", extracted);
    return extracted;
  }

  function setLoading(loading) {
    isLoading = loading;

    console.log("Loading:", loading);

    btnGet.disabled = loading;
    btnGet.classList.toggle("is-loading", loading);

    const moreBtn = $(".js-more-jokes", moreEl);
    if (moreBtn)
      moreBtn.textContent = loading ? "Loading..." : "Give me more jokes";

    btnGet.textContent = loading ? "Loading..." : btnGetLabel;
  }

  function renderFilters() {
    if (!filtersEl || !types.length) {
      filtersEl.innerHTML = "";
      return;
    }

    const options = [
      `<option value="">Type</option>`,
      ...types.map(
        (t) => `
        <option value="${t}"${t === activeType ? " selected" : ""}>
          ${t}
        </option>
      `,
      ),
    ].join("");

    filtersEl.innerHTML = `
      <div class="jokes-filterbar">
        <span class="jokes-filterbar__label">FILTER</span>
        <div class="jokes-filterbar__field">
          <select class="jokes-filterbar__select" data-jokes-type>
            ${options}
          </select>
        </div>
      </div>
    `;

    const select = $("[data-jokes-type]", filtersEl);
    if (select) {
      select.value = activeType || "";
      select.addEventListener("change", (e) => {
        activeType = e.target.value || "";
        console.log("Filter changed to:", activeType || "All");
        renderList();
      });
    }
  }

  function renderList() {
    if (!listEl) return;

    if (!jokes.length) {
      listEl.innerHTML = "";
      return;
    }

    const visible = activeType
      ? jokes.filter((j) => String(j.type) === activeType)
      : jokes;

    console.log(
      "Jokes Full Number:",
      jokes.length,
      "\nVisible Jokes Number:",
      visible.length,
    );

    if (!visible.length) {
      listEl.innerHTML = `<p class="jokes-empty">No jokes for this type.</p>`;
      return;
    }

    listEl.innerHTML = `
      <div class="jokes-items">
        ${visible
          .map((j) => {
            const question = j.setup || "";
            const answer = j.punchline || "";
            const type = j.type || "";
            return `
            <article class="joke">
              <span class="joke__type">${type}</span>
              <div class="joke__content">
                <p class="joke__question">${question}</p>
                <p class="joke__answer">${answer}</p>
              </div>
            </article>
          `;
          })
          .join("")}
      </div>
    `;
  }

  function renderMoreButton() {
    if (!moreEl) return;

    if (!jokes.length) {
      moreEl.innerHTML = "";
      return;
    }

    moreEl.innerHTML = `
      <button type="button" class="wp-block-button__link js-more-jokes">
        Give me more jokes
      </button>
    `;

    const moreBtn = $(".js-more-jokes", moreEl);
    if (moreBtn) {
      moreBtn.addEventListener("click", () => {
        console.log("Loading more jokes...");
        fetchJokes({ append: true });
      });
    }
  }

  async function fetchJokes({ append }) {
    if (isLoading) return;

    if (!window.circusAjax || !circusAjax.ajax_url) {
      console.log("Missing ajax url");
      return;
    }

    console.log("Fetching jokes");

    setLoading(true);

    try {
      const form = new URLSearchParams();
      form.append("action", "circus_get_jokes");

      const res = await fetch(circusAjax.ajax_url, {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
        },
        body: form.toString(),
      });

      console.log("Status:", res.status);

      const json = await res.json();

      console.log("Response:", json);

      if (!json || !json.success || !Array.isArray(json.data)) {
        throw new Error("Invalid API response");
      }

      const newJokes = json.data;

      console.log("Jokes number:", newJokes.length);

      if (append) {
        jokes = jokes.concat(newJokes);
        activeType = "";
        types = types.concat(extractTypes(newJokes));
      } else {
        jokes = newJokes;
        activeType = "";
        types = extractTypes(newJokes);
      }

      renderFilters();
      renderList();
      renderMoreButton();
    } catch (err) {
      console.error("Error while fetching", err);
      listEl.innerHTML = `<p class="jokes-error">Could not load jokes.</p>`;
    } finally {
      setLoading(false);
    }
  }

  btnGet.addEventListener("click", (e) => {
    e.preventDefault();
    console.log("Button clicked");
    fetchJokes({ append: false });
  });
})();