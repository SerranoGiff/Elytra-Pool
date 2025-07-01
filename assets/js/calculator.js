const minimums = {
  3: 100,
  7: 3000,
  15: 7000,
  30: 10000,
  60: 30000,
  90: 50000,
};

const maximums = {
  3: 2999.99,
  7: 6999.99,
  15: 9999.99,
  30: 29999.99,
  60: 49999.99,
  90: Infinity,
};

const dailyPercentRanges = {
  3: [4.2, 4.5],
  7: [4.5, 4.8],
  15: [7.0, 8.0],
  30: [8.5, 9.3],
  60: [10.0, 14.0],
  90: [16.0, 25.0]
};

function updateMinNote() {
  const days = parseInt(document.getElementById('period').value);
  const min = minimums[days];
  const max = maximums[days] === Infinity ? '∞' : `${maximums[days]} USDT`;
  document.getElementById('min-note').innerText = `Minimum: ${min} USDT | Maximum: ${max}`;
}

function saveInputs() {
  localStorage.setItem('crypto', document.getElementById('crypto').value);
  localStorage.setItem('amount', document.getElementById('amount').value);
  localStorage.setItem('period', document.getElementById('period').value);
}

function restoreInputs() {
  const savedCrypto = localStorage.getItem('crypto');
  const savedAmount = localStorage.getItem('amount');
  const savedPeriod = localStorage.getItem('period');

  if (savedCrypto) document.getElementById('crypto').value = savedCrypto;
  if (savedAmount) document.getElementById('amount').value = savedAmount;
  if (savedPeriod) document.getElementById('period').value = savedPeriod;

  updateMinNote();
}

function getRandomDailyRate(min, max) {
  return (Math.random() * (max - min) + min) / 100;
}

async function calculateEarnings() {
  const crypto = document.getElementById('crypto').value;
  const amount = parseFloat(document.getElementById('amount').value);
  const days = parseInt(document.getElementById('period').value);
  const minRequired = minimums[days];
  const maxAllowed = maximums[days];

  const errorMsg = document.getElementById('error-msg');
  const results = document.getElementById('results');
  const spinner = document.getElementById('spinner');
  const btn = document.getElementById('calculateBtn');

  errorMsg.innerText = '';
  results.classList.add('hidden');

  if (!amount || amount < minRequired) {
    errorMsg.innerText = `Minimum for ${days} days is ${minRequired} USDT.`;
    return;
  }

  if (amount > maxAllowed) {
    errorMsg.innerText = `Maximum for ${days} days is ${maxAllowed} USDT.`;
    return;
  }

  saveInputs();
  btn.disabled = true;
  spinner.classList.remove('hidden');
  btn.classList.add('opacity-60', 'cursor-not-allowed');

  try {
    let priceUSD = 0;
    const res = await fetch(`https://api.coingecko.com/api/v3/simple/price?ids=${crypto}&vs_currencies=usd`);
    if (res.ok) {
      const data = await res.json();
      priceUSD = data[crypto]?.usd;
    }

    if (!priceUSD) {
      const fallback = {
        bitcoin: 60000,
        ethereum: 3500,
        solana: 150,
        cardano: 0.5,
        polkadot: 6
      };
      priceUSD = fallback[crypto] || 1;
    }

    const amountCrypto = amount / priceUSD;
    const [minDaily, maxDaily] = dailyPercentRanges[days];
    const dailyRate = getRandomDailyRate(minDaily, maxDaily);

    const rewards = amountCrypto * dailyRate * days;
    const dailyRewards = amountCrypto * dailyRate;
    const total = amountCrypto + rewards;
    const earningsUSDT = rewards * priceUSD;
    const earningsELTR = earningsUSDT * 2;

    const annualRate = (dailyRate * 365 * 100).toFixed(1);

    document.getElementById('apy').innerText = `${annualRate}%`;
    document.getElementById('rewards').innerText = `${rewards.toFixed(6)} ${crypto.toUpperCase()}`;
    document.getElementById('total').innerText = `${total.toFixed(6)} ${crypto.toUpperCase()}`;
    document.getElementById('daily').innerText = `${dailyRewards.toFixed(6)} ${crypto.toUpperCase()}`;
    document.getElementById('earnings-usdt').innerText = `≈ ${earningsUSDT.toFixed(2)} USDT`;
    document.getElementById('earnings-elytra').innerText = `≈ ${earningsELTR.toFixed(2)} ELTR`;

    results.classList.remove('hidden');
  } catch (err) {
    errorMsg.innerText = 'Error fetching price. Please try again.';
    console.error(err);
  } finally {
    btn.disabled = false;
    spinner.classList.add('hidden');
    btn.classList.remove('opacity-60', 'cursor-not-allowed');
  }
}

document.addEventListener('DOMContentLoaded', () => {
  restoreInputs();
  document.getElementById('calculateBtn').addEventListener('click', calculateEarnings);
  document.getElementById('period').addEventListener('change', updateMinNote);
});
