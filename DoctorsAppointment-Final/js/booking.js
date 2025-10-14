document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("booking-form")
  const doctorSelect = document.getElementById("doctor-select")
  const dateInput = document.getElementById("appointment-date")
  const timeSlotsContainer = document.getElementById("time-slots-container")
  const timeSlotsDiv = document.getElementById("time-slots")
  const selectedTimeInput = document.getElementById("selected-time")
  const messagesDiv = document.getElementById("booking-messages")

  // Declare medibook_ajax variable
  const medibook_ajax = {
    ajax_url: "your_ajax_url_here", // Replace with actual AJAX URL
    nonce: "your_nonce_here", // Replace with actual nonce
  }

  // Load time slots when doctor and date are selected
  function loadTimeSlots() {
    const doctorId = doctorSelect.value
    const date = dateInput.value

    if (!doctorId || !date) {
      timeSlotsContainer.style.display = "none"
      return
    }

    timeSlotsDiv.innerHTML =
      '<p style="text-align: center; color: var(--color-text-light);">Loading available times...</p>'
    timeSlotsContainer.style.display = "block"

    const formData = new FormData()
    formData.append("action", "get_time_slots")
    formData.append("nonce", medibook_ajax.nonce)
    formData.append("doctor_id", doctorId)
    formData.append("date", date)

    fetch(medibook_ajax.ajax_url, {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          renderTimeSlots(data.data)
        } else {
          timeSlotsDiv.innerHTML = `<p style="text-align: center; color: var(--color-error);">${data.data.message}</p>`
        }
      })
      .catch((error) => {
        console.error("Error:", error)
        timeSlotsDiv.innerHTML =
          '<p style="text-align: center; color: var(--color-error);">Failed to load time slots</p>'
      })
  }

  // Render time slots
  function renderTimeSlots(slots) {
    if (slots.length === 0) {
      timeSlotsDiv.innerHTML =
        '<p style="text-align: center; color: var(--color-text-light);">No available time slots for this date</p>'
      return
    }

    timeSlotsDiv.innerHTML = ""
    slots.forEach((slot) => {
      const slotDiv = document.createElement("div")
      slotDiv.className = "time-slot" + (slot.available ? "" : " booked")
      slotDiv.textContent = slot.display
      slotDiv.dataset.time = slot.time

      if (slot.available) {
        slotDiv.addEventListener("click", function () {
          // Remove selected class from all slots
          document.querySelectorAll(".time-slot").forEach((s) => s.classList.remove("selected"))
          // Add selected class to clicked slot
          this.classList.add("selected")
          selectedTimeInput.value = this.dataset.time
        })
      }

      timeSlotsDiv.appendChild(slotDiv)
    })
  }

  // Event listeners
  if (doctorSelect) {
    doctorSelect.addEventListener("change", loadTimeSlots)
  }

  if (dateInput) {
    dateInput.addEventListener("change", loadTimeSlots)
  }

  // Form submission
  if (form) {
    form.addEventListener("submit", (e) => {
      e.preventDefault()

      if (!selectedTimeInput.value) {
        showMessage("Please select a time slot", "error")
        return
      }

      const submitBtn = form.querySelector('button[type="submit"]')
      const originalText = submitBtn.textContent
      submitBtn.textContent = "Booking..."
      submitBtn.disabled = true

      const formData = new FormData(form)
      formData.append("action", "book_appointment")
      formData.append("nonce", medibook_ajax.nonce)

      fetch(medibook_ajax.ajax_url, {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            showMessage(data.data.message, "success")
            form.reset()
            timeSlotsContainer.style.display = "none"
            selectedTimeInput.value = ""

            // Scroll to top to see message
            window.scrollTo({ top: 0, behavior: "smooth" })
          } else {
            showMessage(data.data.message, "error")
          }
        })
        .catch((error) => {
          console.error("Error:", error)
          showMessage("Failed to book appointment. Please try again.", "error")
        })
        .finally(() => {
          submitBtn.textContent = originalText
          submitBtn.disabled = false
        })
    })
  }

  // Show message helper
  function showMessage(message, type) {
    const alertClass = type === "success" ? "alert-success" : "alert-error"
    messagesDiv.innerHTML = `<div class="alert ${alertClass}">${message}</div>`

    // Auto-hide after 5 seconds
    setTimeout(() => {
      messagesDiv.innerHTML = ""
    }, 5000)
  }
})
