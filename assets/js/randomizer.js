function flickNumber(id, oldValue, newValue, flicks = 40) {
  const element = document.getElementById(id);
  const direction = newValue > oldValue ? 1 : -1;
  let current = oldValue;
  let count = 0;

  function doStep() {
    // Random chance to pause long (simulate "hinto")
    const longPause = Math.random() < 0.15; // 15% chance
    const delay = longPause
      ? Math.floor(Math.random() * 1000 + 6000) // 6–7 seconds
      : 100 + Math.floor(Math.random() * 200); // 100–300 ms

    setTimeout(() => {
      const step = Math.random() < 0.6 ? 1 : 2;
      current += step * direction;

      // Clamp to target value
      if ((direction === 1 && current >= newValue) || (direction === -1 && current <= newValue)) {
        current = newValue;
      }

      element.textContent = current.toLocaleString();
      count++;

      if (current !== newValue && count < flicks) {
        doStep(); // Continue flicking
      }
    }, delay);
  }

  doStep();
}

let activeUsers = 1000000;

function updateStats() {
  const direction = Math.random() < 0.5 ? -1 : 2;
  const change = Math.floor(Math.random() * 150 + 25) * direction;
  let newActiveUsers = activeUsers + change;

  if (newActiveUsers < 8000) newActiveUsers = 8000;
  if (newActiveUsers > 1500000) newActiveUsers = 1500000;

  flickNumber("active-users", activeUsers, newActiveUsers, 40);
  activeUsers = newActiveUsers;

  const uptime = (Math.random() * (99 - 89) + 89).toFixed(1);
  document.getElementById("uptime").textContent = `${uptime}%`;
}

// Initial call
updateStats();

// Update every 60 seconds (1 minute = 60000 ms)
setInterval(updateStats, 60000);
