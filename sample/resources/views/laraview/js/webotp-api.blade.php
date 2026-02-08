<script type="text/javascript">
  let webOtpStatus = "success";

  if (!window.OTPCredential) {
      webOtpStatus = "feature not available";
  }

  if ('OTPCredential' in window) {
      window.addEventListener('DOMContentLoaded', e => {
          const input = document.querySelector('input[autocomplete="one-time-code"]');
          if (!input) {
              webOtpStatus = "Input Not Found!";
              return;
          }
          const ac = new AbortController();
          const form = input.closest('form');
          if (form) {
              // In case the user manually enters an OTP and submits the form
              form.addEventListener('submit', e => {
                  ac.abort();
              });
          }
          navigator.credentials.get({
              otp: {
                  transport: ['sms']
              },
              signal: ac.signal
          }).then(otp => {
              input.value = otp.code;
              if (form) form.submit();
          }).catch(err => {
              webOtpStatus = err;
          });
      });
  }

  webOtpStatus = "web otp api status: " + webOtpStatus;

  let url = "/ajax/console-log/" + "{{ csrf_token() }}" + "/" + webOtpStatus;

  $.get(url);
</script>