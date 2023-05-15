<script>
  import { SITE } from "~/config.mjs";

  let botField = "";
  let name = "";
  let email = "";
  let message = "";

  let resNameErr = false;
  let resMailErr = false;
  let resMsgErr = false;
  let resCaptchaErr = false;
  $: resSuccess = false;
  $: resMsg = null;
  $: resStatus = null;
  $: loading = false;

  const handleSubmit = async () => {
    grecaptcha.ready(function () {
      grecaptcha
        .execute(SITE.googleCaptchaPublicKey, { action: "submit" })
        .then(async function (token) {
          loading = true;
          var formData = new FormData();
          formData.append("name", name);
          formData.append("mail", email);
          formData.append("message", message);
          formData.append("recaptcha", token);
          const response = await fetch(SITE.origin + `/api/mail.php`, {
            method: "POST",
            body: formData,
          });

          let parsedJson = await response.json();

          resStatus = parsedJson.status;

          if (parsedJson.status == "success") resSuccess = true;
          resMsg = parsedJson.message;
          if (parsedJson.nameErr) resNameErr = "Nombre Error: " + parsedJson.nameErr;
          else resNameErr = null;
          if (parsedJson.mailErr) resMailErr = "Mail Error: " + parsedJson.mailErr;
          else resMailErr = null;
          if (parsedJson.messageErr) resMsgErr = "Mensaje Error: " + parsedJson.messageErr;
          else resMsgErr = null;
          if (parsedJson.captchaErr) resCaptchaErr = "Captcha Error: " + parsedJson.captchaErr;
          else resCaptchaErr = null;
          loading = false;
        })
        .catch((error) => {
          resMsg = error.message;
          submitting = false;
          loading = false;
        });
    });
  };
</script>

<svelte:head>
  <script src={"https://www.google.com/recaptcha/api.js?render=" + SITE.googleCaptchaPublicKey} defer></script>
</svelte:head>

<div class="mx-auto w-full max-w-xl flex-[1_1_100%] rounded-2xl bg-black/20 p-6 backdrop-blur-xl md:flex-[1_1_50%]">
  {#if loading}
    <div class="flex items-center justify-center py-40">
      <div class="fancy-spinner">
        <div class="spinner-ring" />
        <div class="spinner-ring" />
        <div class="spinner-dot" />
      </div>
    </div>
  {/if}
  {#if resSuccess && !loading}
    <div class="mx-auto flex flex-col items-center justify-center py-32">
      <p class="p-6 text-2xl font-bold text-yellow-600 dark:text-yellow-500">
        {resMsg}
      </p>
    </div>
  {:else if !resSuccess && !loading}
    <form on:submit|preventDefault={handleSubmit} class="m-auto flex max-w-lg flex-col items-center text-left">
      <input aria-hidden="true" type="hidden" name="bot-field" bind:value={botField} />
      <div class="mb-2 w-full">
        <label for="name" class="font-bold text-white">Nombre</label>
        <input bind:value={name} class="w-full rounded border bg-slate-100 p-2" required id="name" placeholder="Nombre" title="Nombre" type="text" />
        {#if resNameErr}
          <p class="mt-1 rounded bg-red-400 p-2 text-xs text-black">{resNameErr}</p>
        {/if}
      </div>
      <div class="mb-2 w-full">
        <label for="email" class="font-bold text-white">Email</label>
        <input bind:value={email} class="w-full rounded border bg-slate-100 p-2" required id="email" placeholder="blake@example.com" title="Email" type="email" />
        {#if resMailErr}
          <p class="mt-1 rounded bg-red-400 p-2 text-xs text-black">{resMailErr}</p>
        {/if}
      </div>
      <div class="mb-2 w-full">
        <label for="message" class="font-bold text-white">Mensaje</label>
        <textarea bind:value={message} class="w-full rounded border bg-slate-100 p-2" required id="message" rows={6} placeholder="Escribe tu mensaje aquí..." title="Mensaje" type="text" />
        {#if resMsgErr}
          <p class="mt-1 rounded bg-red-400 p-2 text-xs text-black">{resMsgErr}</p>
        {/if}
      </div>
      <div class="mx-auto mb-4">
        <div id="captcha" />
        {#if resCaptchaErr}
          <p class="mt-1 rounded bg-red-400 p-2 text-xs text-black">{resCaptchaErr}</p>
        {/if}
      </div>
      {#if resStatus == "failed"}
        <p class="mt-1 rounded bg-red-400 p-2 text-xs text-black">
          {resMsg}
        </p>
      {/if}
      <button class="flex w-full items-center justify-center rounded bg-yellow-400 p-4 font-inter font-bold text-black transition hover:bg-yellow-400/90" type="submit">Enviar</button>
    </form>
  {/if}
</div>

<style>
  .fancy-spinner {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 5rem;
    height: 5rem;
  }
  .fancy-spinner div {
    position: absolute;
    width: 4rem;
    height: 4rem;
    border-radius: 50%;
  }
  .fancy-spinner div.spinner-ring {
    border-width: 0.5rem;
    border-style: solid;
    border-color: transparent;
    animation: 3s fancy infinite alternate;
  }
  .fancy-spinner div.spinner-ring:nth-child(1) {
    border-left-color: #eaeaea;
    border-right-color: #eaeaea;
  }
  .fancy-spinner div.spinner-ring:nth-child(2) {
    border-top-color: #eaeaea;
    border-bottom-color: #eaeaea;
    animation-delay: 2s;
  }
  .fancy-spinner div.spinner-dot {
    width: 1rem;
    height: 1rem;
    background: #eaeaea;
  }

  @keyframes fancy {
    to {
      transform: rotate(360deg) scale(0.5);
    }
  }
</style>
