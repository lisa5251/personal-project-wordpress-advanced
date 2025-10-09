;(($) => {
  $(document).ready(() => {
    // Declare dabAdminAjax variable
    const dabAdminAjax = window.dabAdminAjax || {}

    // Handle status change
    $(".status-select").on("change", function () {
      const select = $(this)
      const appointmentId = select.data("id")
      const newStatus = select.val()
      const originalStatus = select.find("option:selected").data("original")

      if (confirm("Are you sure you want to change the status?")) {
        $.ajax({
          url: dabAdminAjax.ajaxurl,
          type: "POST",
          data: {
            action: "dab_update_status",
            nonce: dabAdminAjax.nonce,
            appointment_id: appointmentId,
            status: newStatus,
          },
          success: (response) => {
            if (response.success) {
              // Show success message
              showNotification("Status updated successfully!", "success")

              // Update the row styling based on status
              const row = select.closest("tr")
              row.css("background-color", "#f0fff4")
              setTimeout(() => {
                row.css("background-color", "")
              }, 2000)
            } else {
              showNotification(response.data.message, "error")
              select.val(originalStatus)
            }
          },
          error: () => {
            showNotification("An error occurred. Please try again.", "error")
            select.val(originalStatus)
          },
        })
      } else {
        select.val(originalStatus)
      }
    })

    // Handle appointment deletion
    $(".dab-delete-btn").on("click", function () {
      const btn = $(this)
      const appointmentId = btn.data("id")
      const row = btn.closest("tr")

      if (confirm("Are you sure you want to delete this appointment? This action cannot be undone.")) {
        $.ajax({
          url: dabAdminAjax.ajaxurl,
          type: "POST",
          data: {
            action: "dab_delete_appointment",
            nonce: dabAdminAjax.nonce,
            appointment_id: appointmentId,
          },
          success: (response) => {
            if (response.success) {
              // Fade out and remove the row
              row.fadeOut(400, function () {
                $(this).remove()

                // Check if table is empty
                if ($(".dab-appointments-table tbody tr").length === 0) {
                  location.reload()
                }
              })
              showNotification("Appointment deleted successfully!", "success")
            } else {
              showNotification(response.data.message, "error")
            }
          },
          error: () => {
            showNotification("An error occurred. Please try again.", "error")
          },
        })
      }
    })

    // Show notification function
    function showNotification(message, type) {
      const notification = $('<div class="dab-notification"></div>')
        .addClass(type)
        .text(message)
        .css({
          position: "fixed",
          top: "32px",
          right: "20px",
          padding: "16px 24px",
          borderRadius: "8px",
          fontWeight: "600",
          zIndex: "9999",
          boxShadow: "0 4px 12px rgba(0, 0, 0, 0.15)",
          backgroundColor: type === "success" ? "#48bb78" : "#f56565",
          color: "#ffffff",
        })

      $("body").append(notification)

      setTimeout(() => {
        notification.fadeOut(400, function () {
          $(this).remove()
        })
      }, 3000)
    }

    // Add hover effect to table rows
    $(".dab-appointments-table tbody tr").hover(
      function () {
        $(this).css("background-color", "#f7fafc")
      },
      function () {
        $(this).css("background-color", "")
      },
    )
  })
})(window.jQuery)
