(function () {
  const $ = (sel, root = document) => root.querySelector(sel);

  const jokesRoot = document.querySelector("[data-jokes]");
  const btnGet = document.querySelector(".js-get-jokes");

  if (!jokesRoot || !btnGet) return;

  const filtersEl = $("[data-jokes-filters]", jokesRoot);
  const listEl = $("[data-jokes-list]", jokesRoot);
  const moreEl = $("[data-jokes-more]", jokesRoot);

  let jokes = [];
  let types = [];
  let activeType = "";
  let isLoading = false;

  const btnGetLabel = btnGet.textContent;

  function extractTypes(jokesArr) {
    return jokesArr
      .map((joke) => (joke && joke.type ? String(joke.type) : ""))
      .filter(Boolean)
      .sort();
  }

  function setLoading(loading) {
    isLoading = loading;

    btnGet.disabled = loading;
    btnGet.classList.toggle("is-loading", loading);

    const moreBtn = $(".js-more-jokes", moreEl);
    if (moreBtn)
      moreBtn.textContent = loading ? "Loading..." : "Give me more jokes";

    btnGet.textContent = loading ? "Loading..." : '';
  }

  function renderFilters() {
    if (!filtersEl || !types.length) {
      filtersEl.innerHTML = "";
      return;
    }

    const options = [
      `<option value="">Type</option>`,
      ...types.map(
        (type) => `
        <option value="${type}"${type === activeType ? " selected" : ""}>
          ${type}
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
      ? jokes.filter((joke) => String(joke.type) === activeType)
      : jokes;

    if (!visible.length) {
      listEl.innerHTML = `<p class="jokes-empty">No jokes for this type.</p>`;
      return;
    }

    listEl.innerHTML = `
      <div class="jokes-items">
        ${visible
          .map((joke) => {
            const question = joke.setup || "";
            const answer = joke.punchline || "";
            const type = joke.type || "";
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
        fetchJokes({ append: true });
      });
    }
  }

  async function fetchJokes({ append }) {
    if (isLoading) return;

    if (!window.circusAjax || !circusAjax.ajax_url) return;

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

      const json = await res.json();

      if (!json || !json.success || !Array.isArray(json.data)) {
        throw new Error("Invalid API response");
      }

      const newJokes = json.data;

      if (append) {
        jokes = jokes.concat(newJokes);
        activeType = "";
        types = [...new Set(types.concat(extractTypes(newJokes)))]; 
      } else {
        jokes = newJokes;
        activeType = "";
        types = [...new Set(extractTypes(newJokes))];
      }

      renderFilters();
      renderList();
      renderMoreButton();
    } catch (err) {
      listEl.innerHTML = `<p class="jokes-error">Could not load jokes.</p>`;
    } finally {
      setLoading(false);
    }
  }

  btnGet.addEventListener("click", (e) => {
    e.preventDefault();
    fetchJokes({ append: false });
  });
})();