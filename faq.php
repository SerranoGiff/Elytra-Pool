<?php
session_start();
include 'config/dbcon.php';
// Check if the user is already logged in
if (isset($_SESSION['role']) && $_SESSION['role'] === 'MasterAdmin') {
  header("Location: pages/master admin/masteradmin.php");
  exit;
} elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'Admin') {
  header("Location: pages/admin/admin.php");
  exit;
} elseif (isset($_SESSION['type']) && $_SESSION['type'] === 'Premium') {
  header("Location: pages/PREMIUM ACCOUNT/premium-dashboard.php");
  exit;
} elseif (isset($_SESSION['type'])) {
  header("Location: pages/user.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Elytra Pool | Frequently Asked Questions</title>
  <link rel="stylesheet" href="assets/css/style.css" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
  <link rel="shortcut icon" href="assets/img/ELYTRA.jpg" type="image/x-icon" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .faq-item {
      transition: all 0.3s ease;
    }
    
    .faq-item:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 15px -3px rgba(167, 139, 250, 0.1), 0 4px 6px -2px rgba(167, 139, 250, 0.05);
    }
    
    .faq-content {
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.5s ease, opacity 0.3s ease;
      opacity: 0;
    }
    
    .faq-content.show {
      max-height: 500px;
      opacity: 1;
    }
    
    .rotate-180 {
      transform: rotate(180deg);
    }
 
  </style>
</head>

<body class="min-h-screen bg-gray-950 text-white">
  <!-- Navigation -->
  <nav
    class="fixed top-0 left-0 w-full z-50 backdrop-blur-lg bg-white/10 border-b border-white/10 shadow-md transition-all duration-500 ease-in-out">
    <div class="max-w-7xl mx-auto flex justify-between items-center px-6 py-4">

      <!-- Logo and Title -->
      <a href="index.html" class="flex items-center space-x-3">
        <div class="w-10 h-10 rounded-full overflow-hidden shadow-md hover:scale-105 transition-transform duration-300">
          <img src="assets/img/Elytra Logo.png" alt="Elytra Logo" class="w-full h-full object-cover" />
        </div>
        <span class="text-xl font-semibold tracking-wide text-white hover:text-purple-300 transition duration-300">
          Elytra Pool
        </span>
      </a>

      <!-- Navigation Links with Hover Zoom -->
      <div class="hidden md:flex items-center space-x-8">
        <a href="#home" class="relative group transform hover:scale-105 transition-all duration-300 ease-in-out">
          <span class="text-white hover:text-purple-300 transition">Home</span>
          <span
            class="absolute left-0 -bottom-1 h-0.5 w-0 bg-purple-400 group-hover:w-full transition-all duration-300"></span>
        </a>
        <a href="#staking" class="relative group transform hover:scale-105 transition-all duration-300 ease-in-out">
          <span class="text-white hover:text-purple-300 transition">Staking</span>
          <span
            class="absolute left-0 -bottom-1 h-0.5 w-0 bg-purple-400 group-hover:w-full transition-all duration-300"></span>
        </a>
        <a href="#mining" class="relative group transform hover:scale-105 transition-all duration-300 ease-in-out">
          <span class="text-white hover:text-purple-300 transition">Features</span>
          <span
            class="absolute left-0 -bottom-1 h-0.5 w-0 bg-purple-400 group-hover:w-full transition-all duration-300"></span>
        </a>
        <a href="#earnings" class="relative group transform hover:scale-105 transition-all duration-300 ease-in-out">
          <span class="text-white hover:text-purple-300 transition">Calculator</span>
          <span
            class="absolute left-0 -bottom-1 h-0.5 w-0 bg-purple-400 group-hover:w-full transition-all duration-300"></span>
        </a>
        <a href="about.html#about"
          class="relative group transform hover:scale-105 transition-all duration-300 ease-in-out">
          <span class="text-white hover:text-purple-300 transition">About</span>
          <span
            class="absolute left-0 -bottom-1 h-0.5 w-0 bg-purple-400 group-hover:w-full transition-all duration-300"></span>
        </a>
      </div>

      <!-- Login Button -->
      <section>
        <div class="flex items-center space-x-4">
          <button id="login-button" class="flex items-center space-x-2 px-6 py-2 border border-purple-500 text-purple-400 
             bg-transparent rounded-lg shadow-[0_0_8px_#a855f7]
             hover:bg-purple-600 hover:text-white hover:border-purple-600 
             hover:shadow-[0_0_16px_#9333ea] active:bg-purple-700 
             transition-all duration-300 ease-in-out">
            <i class="fas fa-user transition-colors duration-300"></i>
            <span class="transition-colors duration-300">Login</span>
          </button>
        </div>
      </section>
    </div>
  </nav>

  <!-- Main Login Modal -->
  <div id="loginModal"
    class="fixed inset-0 flex items-center justify-center z-50 hidden transition-opacity duration-300 ease-in-out">
    <div class="bg-gray-800 bg-opacity-75 absolute inset-0"></div>
    <div
      class="bg-white rounded-lg shadow-lg z-10 p-8 max-w-sm w-full overflow-hidden transform transition-transform duration-300 ease-in-out scale-95 opacity-0"
      id="modalContent">
      <div id="modalContentInner">

        <!-- Login Form -->
        <div id="loginForm" class="form-section text-black">
          <h2 class="text-2xl font-bold text-center mb-6">Welcome Back</h2>

          <!-- Notifications -->
          <?php if (isset($_GET['error']) || isset($_GET['success'])): ?>
            <script>
              document.addEventListener("DOMContentLoaded", () => {
                const message = <?= json_encode($_GET['error'] ?? $_GET['success']) ?>;
                const type = <?= isset($_GET['error']) ? json_encode('error') : json_encode('success') ?>;
                showToast(message, type);
                history.replaceState(null, "", window.location.pathname); // remove ?error or ?success
              });
            </script>
          <?php endif; ?>

          <form action="config/login.php" method="POST">
            <div class="mb-4">
              <label for="email" class="block text-sm font-medium">Email Address</label>
              <input type="email" id="email" name="email" class="mt-1 block w-full border border-gray-300 rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-blue-500 text-black" required />
            </div>
            <div class="mb-4">
              <label for="password" class="block text-sm font-medium">Password</label>
              <input type="password" id="password" name="password" class="mt-1 block w-full border border-gray-300 rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-blue-500 text-black" required />
            </div>
            <div class="flex items-center mb-4">
              <input type="checkbox" id="rememberMe" name="rememberMe" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" />
              <label for="rememberMe" class="ml-2 block text-sm cursor-pointer">Remember Me</label>
            </div>
            <div class="mb-4 text-right">
              <button type="button" id="forgotPasswordBtn" class="text-blue-600 hover:underline">Forgot Password?</button>
            </div>
            <button type="submit" class="w-full py-2 rounded-lg text-white font-semibold bg-gradient-to-r from-purple-500 via-indigo-600 to-purple-700 hover:from-indigo-500 hover:via-purple-600 hover:to-indigo-700 transition-all duration-500 ease-in-out">
              Login
            </button>
          </form>

          <div class="mt-4 text-center">
            <button id="switchToSignUp" class="text-blue-600 hover:underline">Don't have an account? Sign Up</button>
          </div>
        </div>

        <!-- Forgot Password Modal -->
        <div id="forgotPasswordModal"
          class="fixed inset-0 flex items-center justify-center z-50 hidden transition-opacity duration-300 ease-in-out">
          <div class="bg-gray-800 bg-opacity-75 absolute inset-0"></div>
          <div
            class="bg-white rounded-lg shadow-lg z-10 p-6 w-full max-w-md transform transition-transform duration-300 ease-in-out scale-95 opacity-0"
            id="forgotPasswordContent">
            <h2 class="text-lg font-bold mb-4 text-black">Reset Your Password</h2>
            <p class="mb-4 text-sm text-gray-700">Enter your email address and we’ll send you a password reset link.
            </p>
            <form id="forgotPasswordForm">
              <input type="email" id="forgotEmail"
                class="w-full mb-4 border border-gray-300 rounded-md p-3 text-black focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Email address" required />
              <div class="flex justify-end gap-2">
                <button type="button" id="cancelForgot"
                  class="px-4 py-2 text-sm bg-gray-300 hover:bg-gray-400 rounded-lg text-black">Cancel</button>
                <button type="submit" class="px-4 py-2 text-sm text-white rounded-lg
         bg-gradient-to-r from-purple-500 via-indigo-600 to-purple-700
         hover:from-indigo-500 hover:via-purple-600 hover:to-indigo-700
         shadow-md hover:shadow-lg hover:shadow-purple-500/40
         transition-all duration-700 ease-in-out">
                  Send Reset Link
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Sign Up Form -->
        <div id="signUpForm" class="form-section hidden text-black">
          <h2 class="text-2xl font-bold text-center mb-6">Create an Account</h2>
          <form action="config/registration.php" method="POST">
            <!-- Update the action to your PHP registration script -->
            <div class="mb-3">
              <label for="username" class="block text-sm font-medium text-gray-700 text-black">Username</label>
              <input type="text" id="username" name="username" required
                class="mt-1 block w-full border border-gray-300 rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
            <div class="mb-3">
              <label for="signUpEmail" class="block text-sm font-medium text-gray-700 text-black">Email Address</label>
              <input type="email" id="signUpEmail" name="email" required
                class="mt-1 block w-full border border-gray-300 rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
            <div class="mb-3">
              <label for="signUpPassword" class="block text-sm font-medium text-gray-700 text-black">Password</label>
              <input type="password" id="signUpPassword" name="password" required
                class="mt-1 block w-full border border-gray-300 rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-blue-500" />
              <p id="passwordStrength" class="mt-1 text-sm"></p>
            </div>
            <div class="mb-3">
              <div class="flex items-center">
                <input type="checkbox" id="agreeTerms" name="agreeTerms"
                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded cursor-pointer" />
                <label for="agreeTerms" class="ml-2 block text-sm text-gray-700 cursor-pointer">I agree to the <span
                    class="text-blue-600 hover:underline">Terms and Conditions</span></label>
              </div>
            </div>
            <div class="mb-3">
              <div class="flex items-center">
                <input type="checkbox" id="agreePrivacy" name="agreePrivacy"
                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded cursor-pointer" />
                <label for="agreePrivacy" class="ml-2 block text-sm text-gray-700 cursor-pointer">I have read and agree
                  to the <span class="text-blue-600 hover:underline">Privacy Policy</span></label>
              </div>
            </div>
            <button type="submit"
              class="w-full py-2 rounded-lg text-white font-semibold bg-gradient-to-r from-purple-500 via-indigo-600 to-purple-700 hover:from-indigo-500 hover:via-purple-600 hover:to-indigo-700 shadow-md hover:shadow-lg hover:shadow-purple-500/40 transition-all duration-700 ease-in-out">Sign
              Up</button>
          </form>
          <div class="mt-4 text-center">
            <button id="switchToLogin" class="text-blue-600 hover:underline">Already have an account? Login</button>
          </div>
        </div>

        <!-- Toast Notification Container -->
        <div id="toastContainer" class="fixed top-4 right-4 z-50 space-y-2"></div>

      </div>
      <div class="mt-4 text-center">
        <button id="closeModal" class="text-gray-500 hover:text-gray-700 transition duration-200">Close</button>
      </div>
    </div>
  </div>

  <!-- Terms and Conditions Modal -->
  <div id="infoModal"
    class="fixed inset-0 flex items-center justify-center z-50 hidden transition-opacity duration-300 ease-in-out">
    <div class="bg-gray-800 bg-opacity-75 absolute inset-0"></div>
    <div
      class="bg-white rounded-lg shadow-lg z-10 p-8 max-w-md w-full overflow-hidden transform transition-transform duration-300 ease-in-out scale-95 opacity-0"
      id="infoModalContent">
      <h2 class="text-gray-800 font-bold mb-4" id="modalTitle">Terms & Conditions</h2>
      <div class="max-h-96 overflow-y-auto" id="termsContent">
        <p id="modalContentText" class="text-gray-700 mb-4">
          <strong>ELYTRA POOL TERMS OF SERVICE</strong><br />
          <strong>Last Updated:</strong> June 22, 2025<br /><br />
          <strong>1. Definitions</strong><br />
          1.1. <strong>"Platform"</strong> refers to Elytra Pool, its affiliates, subsidiaries, and any associated
          decentralized protocols.<br />
          1.2. <strong>"User "</strong> ("you") means any entity interacting with the Platform, including but not
          limited to stakers, referrers, and purchasers of digital assets.<br />
          1.3. <strong>"Rewards"</strong> denote non-guaranteed, variable incentives distributed at the Platform's
          sole discretion, with no expectation of profit.<br /><br />
          <strong>2. Acceptance of Terms</strong><br />
          2.1. By accessing the Platform, you <strong>irrevocably consent</strong> to these Terms, our Privacy
          Policy, and any future amendments (posted without notice).<br />
          2.2. Continued use constitutes <strong>binding arbitration agreement</strong> (waiving class action rights
          per Section 9.4).<br /><br />
          <strong>3. Eligibility & Account Creation</strong><br />
          3.1. Users affirm they are <strong>not</strong> a citizen/resident of prohibited jurisdictions (e.g., USA,
          Cuba, North Korea) unless compliant with local regulations.<br />
          3.2. <strong>No Guaranteed Access</strong>: Accounts may be terminated without explanation (Section
          7.3).<br /><br />
          <strong>4. Staking & Rewards (No Promises)</strong><br />
          4.1. <strong>Variable Rewards</strong>: APY estimates are hypothetical, subject to smart contract risks,
          slashing, and protocol changes.<br />
          4.2. <strong>No Ownership</strong>: Staked assets remain User's property, but rewards are
          <strong>unsecured claims</strong> until distributed.<br />
          4.3. <strong>Tax Liability</strong>: Users alone are responsible for reporting rewards as income (consult
          a tax advisor).<br /><br />
          <strong>5. Referral Program (No Pyramid Schemes)</strong><br />
          5.1. Referral rewards are <strong>limited to 10 levels deep</strong> to avoid regulatory classification as
          a security/Ponzi.<br />
          5.2. Platform reserves the right to <strong>withhold referrals</strong> deemed fraudulent (no
          appeals).<br /><br />
          <strong>6. NFT Avatars & Microtransactions</strong><br />
          6.1. <strong>Non-Refundable</strong>: All purchases of digital assets (e.g., avatars, gems) are
          final.<br />
          6.2. <strong>No Financial Utility</strong>: Avatars confer no staking advantages—purely
          cosmetic.<br /><br />
          <strong>7. Termination & Fund Seizure</strong><br />
          7.1. <strong>At-Will Suspension</strong>: We may freeze accounts for "suspicious activity"
          (undefined).<br />
          7.2. <strong>Abandoned Accounts</strong>: Balances inactive >12 months may be <strong>repurposed as
            protocol fees</strong>.<br /><br />
          <strong>8. Disclaimers (No Liability)</strong><br />
          8.1. <strong>As-Is Service</strong>: The Platform disclaims warranties of merchantability, fitness, or
          non-infringement.<br />
          8.2. <strong>Third-Party Risks</strong>: We are not liable for exploits in underlying blockchains (e.g.,
          Ethereum, Solana).<br /><br />
          <strong>9. Governing Law & Arbitration</strong><br />
          9.1. <strong>Jurisdiction</strong>: Disputes resolved under [Cayman Islands law] (favors
          arbitration).<br />
          9.2. <strong>Class Action Waiver</strong>: Users may only pursue individual claims (no class
          actions).<br /><br />
          <strong>10. Amendments</strong><br />
          10.1. <strong>Unilateral Changes</strong>: We may modify these Terms at any time; continued use =
          acceptance.
        </p>
      </div>
      <div class="text-center mt-4">
        <button id="confirmTerms"
          class="rounded-lg px-4 py-2 transition duration-200 bg-gray-400 text-white cursor-not-allowed" disabled>I
          Accept</button>
      </div>
    </div>
  </div>

  <!-- Privacy Policy Modal -->
  <div id="privacyModal"
    class="fixed inset-0 flex items-center justify-center z-50 hidden transition-opacity duration-300 ease-in-out">
    <div class="bg-gray-800 bg-opacity-75 absolute inset-0"></div>
    <div
      class="bg-white rounded-lg shadow-lg z-10 p-8 max-w-md w-full overflow-hidden transform transition-transform duration-300 ease-in-out scale-95 opacity-0"
      id="privacyModalContent">
      <h2 class="text-gray-800 font-bold mb-4" id="modalTitle">Privacy Policy</h2>
      <div class="max-h-96 overflow-y-auto" id="privacyContent">
        <p id="modalContentText" class="text-gray-700 mb-4">
          <strong>ELYTRA POOL PRIVACY POLICY</strong><br />
          <strong>Last Updated:</strong> June 22, 2025<br /><br />

          <strong>1. Information We Collect</strong><br />
          1.1. We may collect personal information that you provide to us directly, such as your name, email
          address, and any other information you choose to provide.<br />
          1.2. We also collect wallet addresses, on-chain activity, IP addresses, browser/device information, and
          technical metadata.<br /><br />

          <strong>2. How We Use Your Information</strong><br />
          2.1. We use your data to enable core services (staking, transactions).<br />
          2.2. We use data to improve security, detect fraud, and comply with legal obligations.<br />
          2.3. We may send updates or marketing messages (opt-out is available).<br /><br />

          <strong>3. Sharing Your Information</strong><br />
          3.1. We do not sell or rent your personal information.<br />
          3.2. We may share data with third-party vendors (e.g., KYC providers), validators, cloud infrastructure,
          or when legally required.<br /><br />

          <strong>4. Data Retention</strong><br />
          4.1. KYC data is stored for 5 years unless legally mandated otherwise.<br />
          4.2. Wallet activity and public blockchain data are stored indefinitely.<br />
          4.3. Cookies and session data may be retained for up to 30 days.<br /><br />

          <strong>5. Security Measures</strong><br />
          5.1. We implement encryption, secure wallets, and periodic audits.<br />
          5.2. However, no system is 100% secure. Use the platform at your own risk.<br /><br />

          <strong>6. Your Rights</strong><br />
          6.1. Depending on your jurisdiction, you may access, update, or request deletion of your data.<br />
          6.2. You may opt-out of marketing or withdraw consent, though some features may become
          restricted.<br /><br />

          <strong>7. Changes to This Policy</strong><br />
          7.1. We may modify this policy from time to time.<br />
          7.2. Continued use of the platform signifies acceptance of updates.<br /><br />

          <strong>8. Contact Us</strong><br />
          For privacy-related concerns:<br />
          Email: privacy@elytrapool.com<br />
          Website: https://elytrapool.com/contact<br /><br />

          By using Elytra Pool, you acknowledge that you have read and agree to this Privacy Policy.
        </p>
      </div>
      <div class="text-center mt-4">
        <button id="confirmPrivacy"
          class="rounded-lg px-4 py-2 transition duration-200 bg-gray-400 text-white cursor-not-allowed" disabled>I
          Accept</button>
      </div>
    </div>
  </div>

  <button class="md:hidden" id="menu-button" aria-label="Toggle navigation">
    <i class="fas fa-bars text-xl"></i>
  </button>
  </div>
  </nav>

  <!-- FAQ Hero Section -->
  <section class="pt-32 pb-16 px-6 bg-gradient-to-b from-purple-900/20 to-gray-950">
    <div class="max-w-7xl mx-auto text-center">
      <h1 class="text-4xl md:text-5xl font-bold mb-6 text-white">Frequently Asked <span class="text-purple-400">Questions</span></h1>
      <p class="text-lg text-gray-300 mb-8 max-w-3xl mx-auto">
        Find answers to common questions about Elytra Pool's staking platform, rewards system, and account management.
      </p>
      <div class="relative max-w-md mx-auto">
        <input type="text" placeholder="Search FAQs..." class="w-full bg-gray-800 border border-gray-700 rounded-full px-6 py-3 pl-12 focus:outline-none focus:ring-2 focus:ring-purple-500 text-white">
        <i class="fas fa-search absolute left-4 top-3.5 text-gray-400"></i>
      </div>
    </div>
  </section>

  <!-- FAQ Section -->
  <section class="py-12 px-4 md:px-6">
    <div class="max-w-5xl mx-auto">
      <!-- FAQ Categories -->
      <div class="grid md:grid-cols-2 gap-6 mb-12">
        <a href="#general" class="category-card bg-gray-800/50 hover:bg-gray-800 border border-gray-700 rounded-xl p-6 transition-all duration-300 hover:border-purple-500">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 rounded-full bg-purple-500/10 flex items-center justify-center text-purple-400">
              <i class="fas fa-globe text-xl"></i>
            </div>
            <div>
              <h3 class="text-lg font-semibold text-white">General Information</h3>
              <p class="text-sm text-gray-400">Basic platform overview</p>
            </div>
          </div>
        </a>
        
        <a href="#staking" class="category-card bg-gray-800/50 hover:bg-gray-800 border border-gray-700 rounded-xl p-6 transition-all duration-300 hover:border-purple-500">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 rounded-full bg-purple-500/10 flex items-center justify-center text-purple-400">
              <i class="fas fa-coins text-xl"></i>
            </div>
            <div>
              <h3 class="text-lg font-semibold text-white">Staking Process</h3>
              <p class="text-sm text-gray-400">How to stake assets</p>
            </div>
          </div>
        </a>
        
        <a href="#rewards" class="category-card bg-gray-800/50 hover:bg-gray-800 border border-gray-700 rounded-xl p-6 transition-all duration-300 hover:border-purple-500">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 rounded-full bg-purple-500/10 flex items-center justify-center text-purple-400">
              <i class="fas fa-chart-line text-xl"></i>
            </div>
            <div>
              <h3 class="text-lg font-semibold text-white">Rewards System</h3>
              <p class="text-sm text-gray-400">Earnings & payouts</p>
            </div>
          </div>
        </a>
        
        <a href="#security" class="category-card bg-gray-800/50 hover:bg-gray-800 border border-gray-700 rounded-xl p-6 transition-all duration-300 hover:border-purple-500">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 rounded-full bg-purple-500/10 flex items-center justify-center text-purple-400">
              <i class="fas fa-shield-alt text-xl"></i>
            </div>
            <div>
              <h3 class="text-lg font-semibold text-white">Security & Policies</h3>
              <p class="text-sm text-gray-400">Safety measures</p>
            </div>
          </div>
        </a>
      </div>

      <!-- FAQ Content -->
      <div class="space-y-8">
        <!-- General Information -->
        <div id="general" class="scroll-mt-20">
          <h2 class="text-2xl font-bold mb-6 text-white border-b border-gray-800 pb-2 flex items-center">
            <i class="fas fa-globe text-purple-400 mr-3"></i>
            General Information
          </h2>
          <div class="space-y-4">
            <div class="faq-item bg-gray-800/50 border border-gray-700 rounded-xl overflow-hidden transition-all duration-300">
              <button class="faq-question w-full text-left p-6 flex justify-between items-center hover:bg-gray-700/50 transition-colors duration-200">
                <span class="text-lg font-medium text-purple-300">What is Elytra Pool?</span>
                <i class="fas fa-chevron-down text-purple-400 transition-transform duration-300"></i>
              </button>
              <div class="faq-content px-6">
                <div class="pb-6 text-gray-300">
                  Elytra Pool is a decentralized staking platform that allows users to earn passive income by participating in blockchain validation. Our platform offers:
                  <ul class="list-disc pl-5 mt-2 space-y-1">
                    <li>Multi-chain staking support</li>
                    <li>Competitive APY returns</li>
                    <li>Non-custodial asset management</li>
                    <li>Automated reward compounding</li>
                  </ul>
                </div>
              </div>
            </div>

            <div class="faq-item bg-gray-800/50 border border-gray-700 rounded-xl overflow-hidden transition-all duration-300">
              <button class="faq-question w-full text-left p-6 flex justify-between items-center hover:bg-gray-700/50 transition-colors duration-200">
                <span class="text-lg font-medium text-purple-300">What is the ELTR token?</span>
                <i class="fas fa-chevron-down text-purple-400 transition-transform duration-300"></i>
              </button>
              <div class="faq-content px-6">
                <div class="pb-6 text-gray-300">
                  ELTR is the native utility token of the Elytra Pool ecosystem with multiple functions:
                  <ul class="list-disc pl-5 mt-2 space-y-1">
                    <li><strong>Staking:</strong> All staking activities require ELTR</li>
                    <li><strong>Rewards:</strong> Distributed in ELTR with optional auto-compounding</li>
                    <li><strong>Governance:</strong> Voting rights for platform decisions</li>
                    <li><strong>Fee Reduction:</strong> Discounts on transaction fees</li>
                  </ul>
                  The token follows a deflationary model with periodic burns.
                </div>
              </div>
            </div>

            <div class="faq-item bg-gray-800/50 border border-gray-700 rounded-xl overflow-hidden transition-all duration-300">
              <button class="faq-question w-full text-left p-6 flex justify-between items-center hover:bg-gray-700/50 transition-colors duration-200">
                <span class="text-lg font-medium text-purple-300">Which blockchains are supported?</span>
                <i class="fas fa-chevron-down text-purple-400 transition-transform duration-300"></i>
              </button>
              <div class="faq-content px-6">
                <div class="pb-6 text-gray-300">
                  Currently supported networks:
                  <div class="grid grid-cols-2 gap-4 mt-3">
                    <div class="flex items-center space-x-2">
                      <i class="fab fa-ethereum text-blue-400"></i>
                      <span>Ethereum</span>
                    </div>
                    <div class="flex items-center space-x-2">
                      <i class="fab fa-bitcoin text-orange-400"></i>
                      <span>Bitcoin (wrapped)</span>
                    </div>
                    <div class="flex items-center space-x-2">
                      <i class="fas fa-bolt text-purple-400"></i>
                      <span>Solana</span>
                    </div>
                    <div class="flex items-center space-x-2">
                      <i class="fas fa-link text-blue-300"></i>
                      <span>Chainlink</span>
                    </div>
                  </div>
                  <p class="mt-4">More chains are added quarterly based on community voting.</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Staking Process -->
        <div id="staking" class="scroll-mt-20">
          <h2 class="text-2xl font-bold mb-6 text-white border-b border-gray-800 pb-2 flex items-center">
            <i class="fas fa-coins text-purple-400 mr-3"></i>
            Staking Process
          </h2>
          <div class="space-y-4">
            <div class="faq-item bg-gray-800/50 border border-gray-700 rounded-xl overflow-hidden transition-all duration-300">
              <button class="faq-question w-full text-left p-6 flex justify-between items-center hover:bg-gray-700/50 transition-colors duration-200">
                <span class="text-lg font-medium text-purple-300">How do I stake assets on Elytra Pool?</span>
                <i class="fas fa-chevron-down text-purple-400 transition-transform duration-300"></i>
              </button>
              <div class="faq-content px-6">
                <div class="pb-6 text-gray-300">
                  <p>Our staking process is designed for simplicity:</p>
                  <ol class="list-decimal pl-5 mt-2 space-y-2">
                    <li><strong>Deposit:</strong> Transfer supported assets (USDT, ETH, BTC, etc.) to your Elytra wallet</li>
                    <li><strong>Convert:</strong> Assets are automatically converted to ELTR at market rates (1.9% conversion fee for premium user)</li>
                    <li><strong>Select Pool:</strong> Choose from our curated staking pools with varying lock periods</li>
                    <li><strong>Confirm:</strong> Approve the staking transaction via your Elytra wallet</li>
                  </ol>
                  <p class="mt-3 text-sm text-purple-300">Note: Only ELTR tokens can be staked directly. Other assets are automatically converted.</p>
                </div>
              </div>
            </div>

            <div class="faq-item bg-gray-800/50 border border-gray-700 rounded-xl overflow-hidden transition-all duration-300">
              <button class="faq-question w-full text-left p-6 flex justify-between items-center hover:bg-gray-700/50 transition-colors duration-200">
                <span class="text-lg font-medium text-purple-300">What are the different staking pool types?</span>
                <i class="fas fa-chevron-down text-purple-400 transition-transform duration-300"></i>
              </button>
              <div class="faq-content px-6">
                <div class="pb-6 text-gray-300">
                  We offer two staking pool types:
                  
                    <div class="bg-gray-800/50 p-4 rounded-lg border-l-4 border-blue-400">
                      <h4 class="font-semibold text-white">Standard Staking (Free)</h4>
                      <p class="text-sm mt-1">- Higher APY (scales with duration)<br>- Can only stake one at a time<br>- Limited Access</p>
                    </div>
                    <div class="bg-gray-800/50 p-4 rounded-lg border-l-4 border-purple-400">
                      <h4 class="font-semibold text-white">VIP Staking (Premium users)</h4>
                      <p class="text-sm mt-1">- Highest APY rates<br>- Unlimited Staking<br>- Priority support<br>- Custom strategies</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="faq-item bg-gray-800/50 border border-gray-700 rounded-xl overflow-hidden transition-all duration-300">
              <button class="faq-question w-full text-left p-6 flex justify-between items-center hover:bg-gray-700/50 transition-colors duration-200">
                <span class="text-lg font-medium text-purple-300">What is the minimum staking amount?</span>
                <i class="fas fa-chevron-down text-purple-400 transition-transform duration-300"></i>
              </button>
              <div class="faq-content px-6">
                <div class="pb-6 text-gray-300">
                  <p>Minimum staking requirements vary by pool type:</p>
                  <table class="w-full mt-3 border-collapse">
                    <thead>
                      <tr class="bg-gray-800 text-left">
                        <th class="p-3 border-b border-gray-700">Pool Type</th>
                        <th class="p-3 border-b border-gray-700">Minimum ELTR</th>
                        <th class="p-3 border-b border-gray-700">USDT Equivalent</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr class="border-b border-gray-800">
                        <td class="p-3">Flexible</td>
                        <td class="p-3">200 ELTR</td>
                        <td class="p-3">≈100 USDT</td>
                      </tr>
                      <tr class="border-b border-gray-800">
                        <td class="p-3">7-day Locked</td>
                        <td class="p-3">3000 ELTR</td>
                        <td class="p-3">≈1500 USDT</td>
                      </tr>
                      <tr>
                        <td class="p-3">30-day Locked</td>
                        <td class="p-3">10000 ELTR</td>
                        <td class="p-3">≈5000 USD</td>
                      </tr>
                    </tbody>
                  </table>
                  <p class="mt-3 text-sm">Note: Minimums may change based on network conditions.</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Rewards System -->
        <div id="rewards" class="scroll-mt-20">
          <h2 class="text-2xl font-bold mb-6 text-white border-b border-gray-800 pb-2 flex items-center">
            <i class="fas fa-chart-line text-purple-400 mr-3"></i>
            Rewards System
          </h2>
          <div class="space-y-4">
            <div class="faq-item bg-gray-800/50 border border-gray-700 rounded-xl overflow-hidden transition-all duration-300">
              <button class="faq-question w-full text-left p-6 flex justify-between items-center hover:bg-gray-700/50 transition-colors duration-200">
                <span class="text-lg font-medium text-purple-300">How are staking rewards calculated?</span>
                <i class="fas fa-chevron-down text-purple-400 transition-transform duration-300"></i>
              </button>
              <div class="faq-content px-6">
                <div class="pb-6 text-gray-300">
                  <p>Rewards follow this formula:</p>
                  <div class="bg-gray-800 p-4 rounded-lg my-3 font-mono">
                    (Your Stake ÷ Total Pool Size) × Daily Reward Pool × (1 - Platform Fee)
                  </div>
                  <p>Key factors affecting rewards:</p>
                  <ul class="list-disc pl-5 mt-2 space-y-1">
                    <li><strong>Stake duration:</strong> Longer locks = higher multipliers</li>
                    <li><strong>Pool performance:</strong> Varies by blockchain activity</li>
                    <li><strong>Platform fee:</strong> 2% for standard users, 1% for Premium</li>
                    <li><strong>Compounding:</strong> Auto-compound boosts effective APY</li>
                  </ul>
                </div>
              </div>
            </div>

            <div class="faq-item bg-gray-800/50 border border-gray-700 rounded-xl overflow-hidden transition-all duration-300">
              <button class="faq-question w-full text-left p-6 flex justify-between items-center hover:bg-gray-700/50 transition-colors duration-200">
                <span class="text-lg font-medium text-purple-300">When and how are rewards distributed?</span>
                <i class="fas fa-chevron-down text-purple-400 transition-transform duration-300"></i>
              </button>
              <div class="faq-content px-6">
                <div class="pb-6 text-gray-300">
                  <p>Reward distribution schedule:</p>
                  <div class="mt-3 space-y-3">
                    <div class="flex items-start">
                      <div class="bg-purple-500/10 text-purple-400 rounded-full p-1 mr-3 mt-1">
                        <i class="fas fa-clock text-xs"></i>
                      </div>
                      <div>
                        <h4 class="font-medium">Weekly Payouts</h4>
                        <p class="text-sm text-gray-400">Credited every 24 hours at 00:00 UTC</p>
                      </div>
                    </div>
                    <div class="flex items-start">
                      <div class="bg-purple-500/10 text-purple-400 rounded-full p-1 mr-3 mt-1">
                        <i class="fas fa-wallet text-xs"></i>
                      </div>
                      <div>
                        <h4 class="font-medium">Distribution Methods</h4>
                        <p class="text-sm text-gray-400">Auto-staked by default or claimable to wallet</p>
                      </div>
                    </div>
                    <div class="flex items-start">
                      <div class="bg-purple-500/10 text-purple-400 rounded-full p-1 mr-3 mt-1">
                        <i class="fas fa-bell text-xs"></i>
                      </div>
                      <div>
                        <h4 class="font-medium">Notifications</h4>
                        <p class="text-sm text-gray-400">Email/web notifications for each distribution</p>
                      </div>
                    </div>
                  </div>
                  <p class="mt-4">Rewards appear in your dashboard within 5 minutes of distribution.</p>
                </div>
              </div>
            </div>

            <div class="faq-item bg-gray-800/50 border border-gray-700 rounded-xl overflow-hidden transition-all duration-300">
              <button class="faq-question w-full text-left p-6 flex justify-between items-center hover:bg-gray-700/50 transition-colors duration-200">
                <span class="text-lg font-medium text-purple-300">What is the difference between APY and APR?</span>
                <i class="fas fa-chevron-down text-purple-400 transition-transform duration-300"></i>
              </button>
              <div class="faq-content px-6">
                <div class="pb-6 text-gray-300">
                  <div class="grid md:grid-cols-2 gap-6">
                    <div class="bg-gray-800/50 p-4 rounded-lg">
                      <h4 class="font-semibold text-purple-300 mb-2">APR (Annual Percentage Rate)</h4>
                      <ul class="list-disc pl-5 space-y-1 text-sm">
                        <li>Simple interest rate</li>
                        <li>Doesn't account for compounding</li>
                        <li>Fixed rate for locked staking</li>
                        <li>Easier to calculate</li>
                      </ul>
                    </div>
                    <div class="bg-gray-800/50 p-4 rounded-lg">
                      <h4 class="font-semibold text-purple-300 mb-2">APY (Annual Percentage Yield)</h4>
                      <ul class="list-disc pl-5 space-y-1 text-sm">
                        <li>Includes compounding effects</li>
                        <li>Reflects actual earnings</li>
                        <li>Variable for flexible staking</li>
                        <li>Higher than APR with compounding</li>
                      </ul>
                    </div>
                  </div>
                  <p class="mt-4">Fun Fact: We Use Daily Earnings Percentage so you don't have to worry calculating it!</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Security & Policies -->
        <div id="security" class="scroll-mt-20">
          <h2 class="text-2xl font-bold mb-6 text-white border-b border-gray-800 pb-2 flex items-center">
            <i class="fas fa-shield-alt text-purple-400 mr-3"></i>
            Security & Policies
          </h2>
          <div class="space-y-4">
            <div class="faq-item bg-gray-800/50 border border-gray-700 rounded-xl overflow-hidden transition-all duration-300">
              <button class="faq-question w-full text-left p-6 flex justify-between items-center hover:bg-gray-700/50 transition-colors duration-200">
                <span class="text-lg font-medium text-purple-300">What security measures protect my assets?</span>
                <i class="fas fa-chevron-down text-purple-400 transition-transform duration-300"></i>
              </button>
              <div class="faq-content px-6">
                <div class="pb-6 text-gray-300">
                  <p>Elytra Pool employs multiple security layers:</p>
                  <div class="mt-4 space-y-4">
                    <div class="flex items-start">
                      <div class="bg-green-500/10 text-green-400 rounded-full p-1 mr-3 mt-1">
                        <i class="fas fa-lock text-xs"></i>
                      </div>
                      <div>
                        <h4 class="font-medium">Non-Custodial Staking</h4>
                        <p class="text-sm text-gray-400">You retain control of private keys</p>
                      </div>
                    </div>
                    <div class="flex items-start">
                      <div class="bg-blue-500/10 text-blue-400 rounded-full p-1 mr-3 mt-1">
                        <i class="fas fa-shield-alt text-xs"></i>
                      </div>
                      <div>
                        <h4 class="font-medium">Smart Contract Audits</h4>
                        <p class="text-sm text-gray-400">Quarterly audits by CertiK & Hacken</p>
                      </div>
                    </div>
                    <div class="flex items-start">
                      <div class="bg-purple-500/10 text-purple-400 rounded-full p-1 mr-3 mt-1">
                        <i class="fas fa-user-shield text-xs"></i>
                      </div>
                      <div>
                        <h4 class="font-medium">2FA Authentication</h4>
                        <p class="text-sm text-gray-400">Required for withdrawals</p>
                      </div>
                    </div>
                    <div class="flex items-start">
                      <div class="bg-red-500/10 text-red-400 rounded-full p-1 mr-3 mt-1">
                        <i class="fas fa-bell text-xs"></i>
                      </div>
                      <div>
                        <h4 class="font-medium">Activity Monitoring</h4>
                        <p class="text-sm text-gray-400">Real-time suspicious activity alerts</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="faq-item bg-gray-800/50 border border-gray-700 rounded-xl overflow-hidden transition-all duration-300">
              <button class="faq-question w-full text-left p-6 flex justify-between items-center hover:bg-gray-700/50 transition-colors duration-200">
                <span class="text-lg font-medium text-purple-300">What happens if I unstake early?</span>
                <i class="fas fa-chevron-down text-purple-400 transition-transform duration-300"></i>
              </button>
              <div class="faq-content px-6">
                <div class="pb-6 text-gray-300">
                  <p>Early unstaking policies vary by pool type:</p>
                    <div class="bg-gray-800/50 p-4 rounded-lg border-l-4 border-yellow-400">
                      <h4 class="font-semibold text-white">Locked Pools (Standard)</h4>
                      <p class="text-sm mt-1">- early unstaking fee<br>- 14-day processing period<br>- Forfeiture of current cycle rewards</p>
                    </div>
                    <div class="bg-gray-800/50 p-4 rounded-lg border-l-4 border-red-400">
                      <h4 class="font-semibold text-white">VIP Locked Pools</h4>
                      <p class="text-sm mt-1">- early unstaking fee<br>- 7-day processing period<br>- Forfeiture of current + next cycle rewards</p>
                    </div>
                  </div>
                  <p class="mt-4 text-sm text-purple-300">Note: Fees are deducted from principal, not rewards.</p>
                </div>
              </div>
            </div>

            <div class="faq-item bg-gray-800/50 border border-gray-700 rounded-xl overflow-hidden transition-all duration-300">
              <button class="faq-question w-full text-left p-6 flex justify-between items-center hover:bg-gray-700/50 transition-colors duration-200">
                <span class="text-lg font-medium text-purple-300">What is your withdrawal verification process?</span>
                <i class="fas fa-chevron-down text-purple-400 transition-transform duration-300"></i>
              </button>
              <div class="faq-content px-6">
                <div class="pb-6 text-gray-300">
                  <p>For security, we require:</p>
                  <ol class="list-decimal pl-5 mt-2 space-y-2">
                    <li><strong>Initial Verification:</strong>
                      <ul class="list-disc pl-5 mt-1 space-y-1">
                        <li>Email confirmation</li>
                        <li>Wallet signature verification</li>
                      </ul>
                    </li>
                    <li><strong>For Withdrawals</strong>
                      <ul class="list-disc pl-5 mt-1 space-y-1">
                        <li>ID document verification (KYC)</li>
                        <li>24-hour security hold</li>
                        <li>Manual review by security team</li>
                      </ul>
                    </li>
                    <li><strong>Ongoing Security:</strong>
                      <ul class="list-disc pl-5 mt-1 space-y-1">
                        <li>Withdrawal address whitelisting</li>
                        <li>IP change notifications</li>
                        <li>Multi-signature approvals</li>
                      </ul>
                    </li>
                  </ol>
                  <p class="mt-3 text-sm">Standard withdrawals process within 24 hours after verification.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Support Section -->
      <div class="mt-16 bg-gray-800/50 border border-gray-700 rounded-xl p-8 text-center">
        <div class="max-w-2xl mx-auto">
          <h2 class="text-2xl font-bold text-white mb-2">Still have questions?</h2>
          <p class="text-gray-300 mb-6">Our support team is available 24/7 to assist you.</p>
          <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="mailto:support@elytrapool.com" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2">
              <i class="fas fa-envelope"></i>
              <span>Email Support</span>
            </a>
            <a href="https://t.me/elytrapool_support" target="_blank" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2">
              <i class="fab fa-telegram"></i>
              <span>Live Chat</span>
            </a>
          </div>
          <p class="mt-6 text-sm text-gray-500">Last Updated: <?= date("F j, Y") ?></p>
        </div>
      </div>
    </div>
  </section>

<!-- Footer -->
  <footer class="py-12 px-6 border-t border-gray-800">
    <div class="max-w-7xl mx-auto">
      <div class="grid md:grid-cols-4 gap-8 mb-8">
        <div class="flex items-center space-x-2">
          <a href="index.html" class="flex items-center">
            <div
              class="w-10 h-10 rounded-full flex items-center justify-center pulse hover:scale-105 transition-transform duration-200">
              <img src="assets/img/Elytra Logo.png" alt="Elytra Logo" class="w-full h-full rounded-full object-cover" />
            </div>
            <span class="text-xl font-bold text-white hover:text-blue-200 transition-colors duration-200">Elytra
              Pool</span>
          </a>
        </div>

        <div>
          <h4 class="font-semibold mb-4">Products</h4>
          <ul class="space-y-2 text-sm text-gray-400">
            <li><a href="#staking" class="hover:text-white">Staking</a></li>
            <li><a href="#mining" class="hover:text-white">Premium</a></li>
            <li><a href="#earnings" class="hover:text-white">Calculator</a></li>
            <li><a href="pages/about.html" class="hover:text-white">FAQ</a></li>
          </ul>
        </div>

        <div>
          <h4 class="font-semibold mb-4">Support</h4>
          <ul class="space-y-2 text-sm text-gray-400">
            <li><a href="#" class="hover:text-white">Help Center</a></li>
            <li><a href="#" class="hover:text-white">Contact Us</a></li>
            <li><a href="#" class="hover:text-white">Status</a></li>
            <li><a href="#" class="hover:text-white">Privacy Policy</a></li>
          </ul>
        </div>

        <div>
          <h4 class="font-semibold mb-4">Follow Us</h4>
          <ul class="space-y-2 text-sm text-gray-400">
            <li><a href="#" class="hover:text-white">Twitter</a></li>
            <li><a href="#" class="hover:text-white">Telegram</a></li>
            <li><a href="#" class="hover:text-white">Discord</a></li>
          </ul>
        </div>
      </div>

      <div class="pt-8 border-t border-gray-800 flex flex-col md:flex-row justify-between items-center">
        <div class="text-sm text-gray-400 mb-4 md:mb-0">© 2023 - 2025 Elytra Pool. All rights reserved.</div>
        <div class="flex space-x-6">
          <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-twitter"></i></a>
          <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-telegram"></i></a>
          <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-discord"></i></a>
        </div>
      </div>
    </div>
  </footer>

  <!-- Scripts -->
  <script>
    // FAQ Toggle Functionality
    document.querySelectorAll('.faq-question').forEach(button => {
      button.addEventListener('click', () => {
        const faqItem = button.closest('.faq-item');
        const content = faqItem.querySelector('.faq-content');
        const icon = button.querySelector('i');
        
        // Toggle the current item
        content.classList.toggle('show');
        icon.classList.toggle('rotate-180');
        
        // Close other open items
        document.querySelectorAll('.faq-item').forEach(item => {
          if (item !== faqItem) {
            item.querySelector('.faq-content').classList.remove('show');
            item.querySelector('i').classList.remove('rotate-180');
          }
        });
      });
    });

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
          behavior: 'smooth'
        });
      });
    });

    // Search functionality
    const searchInput = document.querySelector('input[placeholder="Search FAQs..."]');
    searchInput.addEventListener('input', (e) => {
      const searchTerm = e.target.value.toLowerCase();
      
      document.querySelectorAll('.faq-item').forEach(item => {
        const question = item.querySelector('.faq-question span').textContent.toLowerCase();
        const answer = item.querySelector('.faq-content').textContent.toLowerCase();
        
        if (question.includes(searchTerm) || answer.includes(searchTerm)) {
          item.style.display = 'block';
          // Highlight matching text
          const regex = new RegExp(searchTerm, 'gi');
          item.innerHTML = item.innerHTML.replace(regex, match => `<span class="bg-yellow-500/20 text-yellow-300">${match}</span>`);
        } else {
          item.style.display = 'none';
        }
      });
    });
  </script>
  <script src="assets/js/script.js"></script>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>

</html>