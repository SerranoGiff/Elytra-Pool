// staking-script.js

const stakingModal = document.getElementById('stakingModal');
const closeModalBtn = document.getElementById('closeModal');
const stakeButtons = document.querySelectorAll('.stake-now');
const lockPeriodSelect = document.getElementById('lockPeriodSelect');
const modalElytra = document.getElementById('modalElytra');
const modalUSDT = document.getElementById('modalUSDT');
const modalAPY = document.getElementById('modalAPY');
const stakeAmount = document.getElementById('stakeAmount');
const amountRangeInfo = document.getElementById('amountRangeInfo');
const confirmStakeBtn = document.querySelector('.staking-modal-btn');

const toastNotification = document.getElementById('toastNotification');
const errorToast = document.getElementById('errorToast');

const DAE = {
  3: '3.5% - 4.0%',
  7: '4.0% - 4.3%',
  15: '4.2% - 4.55%',
  30: '4.5% - 5.0%',
  60: '5.2% - 5.5%',
  90: '6.0% - 6.5%',
};

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

let selectedCoin = 'USDT'; // Default

stakeButtons.forEach(button => {
  button.addEventListener('click', () => {
    selectedCoin = button.getAttribute('data-coin') || 'USDT';
    const period = parseInt(button.getAttribute('data-period'), 10);

    lockPeriodSelect.value = period;
    updateModalValues(period);
    openModal();
  });
});

lockPeriodSelect.addEventListener('change', () => {
  const selected = parseInt(lockPeriodSelect.value, 10);
  updateModalValues(selected);
});

closeModalBtn.addEventListener('click', () => {
  stakingModal.classList.add('hidden');
});

confirmStakeBtn.addEventListener('click', () => {
  const selected = parseInt(lockPeriodSelect.value, 10);
  const min = minimums[selected];
  const max = maximums[selected];
  const amount = parseFloat(stakeAmount.value);

  if (isNaN(amount) || amount < min || amount > max) {
    showToast(errorToast);
    return;
  }

  stakingModal.classList.add('hidden');
  showToast(toastNotification);
  stakeAmount.value = '';
});

function updateModalValues(period) {
  const min = minimums[period];
  const max = maximums[period];

  modalElytra.textContent = `${(min / 2).toFixed(0)} ELYTRA`;
  modalUSDT.textContent = `${min} ${selectedCoin}`;
  modalAPY.textContent = DAE[period];
  amountRangeInfo.textContent = `Min: ${min} | Max: ${max === Infinity ? '∞' : max}`;
}

function openModal() {
  stakingModal.classList.remove('hidden');
}

function showToast(toast) {
  toast.classList.remove('hidden');
  setTimeout(() => toast.classList.add('hidden'), 3000);
}
