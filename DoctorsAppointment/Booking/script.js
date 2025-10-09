;(($) => {
  // Declare jQuery variable
  const jQuery = window.jQuery

  // Declare dabAjax variable
  const dabAjax = window.dabAjax

  jQuery(document).ready(($) => {
    // Set minimum date to today
    const today = new Date().toISOString().split("T")[0]
    $("#appointment_date").attr("min", today)

    // Handle form submission
    $("#dab-booking-form").on("submit", function (e) {
      e.preventDefault()

      const form = $(this)
      const submitBtn = form.find(".dab-submit-btn")
      const btnText = submitBtn.find(".btn-text")
      const btnLoader = submitBtn.find(".btn-loader")
      const messageDiv = $("#dab-form-message")

      // Disable submit button
      submitBtn.prop("disabled", true)
      btnText.hide()
      btnLoader.show()
      messageDiv.removeClass("success error").hide()

      // Prepare form data
      const formData = {
        action: "dab_book_appointment",
        nonce: dabAjax.nonce,
        patient_name: $("#patient_name").val(),
        patient_email: $("#patient_email").val(),
        patient_phone: $("#patient_phone").val(),
        appointment_date: $("#appointment_date").val(),
        appointment_time: $("#appointment_time").val(),
        doctor_name: $("#doctor_name").val(),
        reason: $("#reason").val(),
      }

      // Send AJAX request
      $.ajax({
        url: dabAjax.ajaxurl,
        type: "POST",
        data: formData,
        success: (response) => {
          if (response.success) {
            messageDiv.addClass("success").text(response.data.message).show()
            form[0].reset()

            // Scroll to message
            $("html, body").animate(
              {
                scrollTop: messageDiv.offset().top - 100,
              },
              500,
            )
          } else {
            messageDiv.addClass("error").text(response.data.message).show()
          }
        },
        error: () => {
          messageDiv.addClass("error").text("An error occurred. Please try again.").show()
        },
        complete: () => {
          submitBtn.prop("disabled", false)
          btnText.show()
          btnLoader.hide()
        },
      })
    })

    // Add input validation feedback
    $("input[required], select[required], textarea[required]").on("blur", function () {
      const input = $(this)
      if (input.val().trim() === "") {
        input.css("border-color", "#fc8181")
      } else {
        input.css("border-color", "#48bb78")
      }
    })

    // Reset border color on focus
    $("input, select, textarea").on("focus", function () {
      $(this).css("border-color", "#3182ce")
    })
  })
})(window.jQuery)
