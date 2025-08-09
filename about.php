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
  <title>Elytra Pool | About Us</title>
  <link rel="stylesheet" href="assets/css/style.css" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
  <link rel="shortcut icon" href="assets/img/ELYTRA.jpg" type="image/x-icon" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .card-glass {
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      transition: all 0.3s ease;
    }

    .card-glass:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(167, 139, 250, 0.3);
      border-color: rgba(167, 139, 250, 0.3);
    }

    .fade-in {
      animation: fadeIn 0.5s ease forwards;
      opacity: 0;
    }

    @keyframes fadeIn {
      to {
        opacity: 1;
      }
    }

    .section-title {
      position: relative;
      display: inline-block;
    }

    .section-title:after {
      content: '';
      position: absolute;
      bottom: -8px;
      left: 0;
      width: 50%;
      height: 3px;
      background: linear-gradient(90deg, #7c3aed, #a855f7);
      border-radius: 3px;
    }

    .team-img {
      width: 120px;
      height: 120px;
      object-fit: cover;
      border: 3px solid rgba(167, 139, 250, 0.3);
      transition: all 0.3s ease;
    }

    .team-member:hover .team-img {
      transform: scale(1.05);
      border-color: #a855f7;
    }
  </style>
</head>

<body class="min-h-screen bg-gray-950 text-white">
  <!-- Navigation -->
  <nav class="fixed top-0 left-0 w-full z-50 backdrop-blur-lg bg-white/10 border-b border-white/10 shadow-md transition-all duration-500 ease-in-out">
    <div class="max-w-7xl mx-auto flex justify-between items-center px-6 py-4">

      <!-- Logo and Title -->
      <a href="index.php" class="flex items-center space-x-3">
        <div class="w-10 h-10 rounded-full overflow-hidden shadow-md hover:scale-105 transition-transform duration-300">
          <img src="assets/img/Elytra Logo.png" alt="Elytra Logo" class="w-full h-full object-cover" />
        </div>
        <span class="text-xl font-semibold tracking-wide text-white hover:text-purple-300 transition duration-300">
          Elytra Pool
        </span>
      </a>

      <!-- Desktop Navigation Links -->
      <div class="hidden md:flex items-center space-x-8">
        <a href="index.php#home" class="relative group transform hover:scale-105 transition-all duration-300 ease-in-out">
          <span class="text-white hover:text-purple-300 transition">Home</span>
          <span class="absolute left-0 -bottom-1 h-0.5 w-0 bg-purple-400 group-hover:w-full transition-all duration-300"></span>
        </a>
        <a href="index.php#staking" class="relative group transform hover:scale-105 transition-all duration-300 ease-in-out">
          <span class="text-white hover:text-purple-300 transition">Staking</span>
          <span class="absolute left-0 -bottom-1 h-0.5 w-0 bg-purple-400 group-hover:w-full transition-all duration-300"></span>
        </a>
        <a href="index.php#mining" class="relative group transform hover:scale-105 transition-all duration-300 ease-in-out">
          <span class="text-white hover:text-purple-300 transition">Features</span>
          <span class="absolute left-0 -bottom-1 h-0.5 w-0 bg-purple-400 group-hover:w-full transition-all duration-300"></span>
        </a>
        <a href="index.php#earnings" class="relative group transform hover:scale-105 transition-all duration-300 ease-in-out">
          <span class="text-white hover:text-purple-300 transition">Calculator</span>
          <span class="absolute left-0 -bottom-1 h-0.5 w-0 bg-purple-400 group-hover:w-full transition-all duration-300"></span>
        </a>
        <a href="about.php" class="relative group transform hover:scale-105 transition-all duration-300 ease-in-out">
          <span class="text-white hover:text-purple-300 transition">About</span>
          <span class="absolute left-0 -bottom-1 h-0.5 w-0 bg-purple-400 group-hover:w-full transition-all duration-300"></span>
        </a>
      </div>

      <!-- Login Button and Mobile Toggle -->
      <div class="flex items-center space-x-2">
        <!-- Mobile Menu Toggle -->
        <button id="mobile-menu-button" class="md:hidden text-white text-2xl ml-2">
          <i class="fas fa-bars"></i>
        </button>

        <button id="login-button" class="flex items-center space-x-2 px-4 py-2 border border-purple-500 text-purple-400 
        bg-transparent rounded-lg shadow-[0_0_8px_#a855f7]
        hover:bg-purple-600 hover:text-white hover:border-purple-600 
        hover:shadow-[0_0_16px_#9333ea] active:bg-purple-700 
        transition-all duration-300 ease-in-out">
          <i class="fas fa-user transition-colors duration-300"></i>
          <span class="hidden sm:inline transition-colors duration-300">Login</span>
        </button>
      </div>
    </div>

    <!-- Mobile Dropdown Menu -->
    <div id="mobile-menu" class="md:hidden hidden text-white text-center animate-fade-in">
      <a href="index.php#home" class="block px-6 py-4 transition-all duration-300 transform hover:scale-105 hover:text-purple-300 relative group">
        Home
        <span class="absolute left-0 bottom-0 w-0 h-0.5 bg-purple-400 group-hover:w-full transition-all duration-300"></span>
      </a>
      <a href="index.php#staking" class="block px-6 py-4 transition-all duration-300 transform hover:scale-105 hover:text-purple-300 relative group">
        Staking
        <span class="absolute left-0 bottom-0 w-0 h-0.5 bg-purple-400 group-hover:w-full transition-all duration-300"></span>
      </a>
      <a href="index.php#mining" class="block px-6 py-4 transition-all duration-300 transform hover:scale-105 hover:text-purple-300 relative group">
        Features
        <span class="absolute left-0 bottom-0 w-0 h-0.5 bg-purple-400 group-hover:w-full transition-all duration-300"></span>
      </a>
      <a href="index.php#earnings" class="block px-6 py-4 transition-all duration-300 transform hover:scale-105 hover:text-purple-300 relative group">
        Calculator
        <span class="absolute left-0 bottom-0 w-0 h-0.5 bg-purple-400 group-hover:w-full transition-all duration-300"></span>
      </a>
      <a href="about.php" class="block px-6 py-4 transition-all duration-300 transform hover:scale-105 hover:text-purple-300 relative group">
        About
        <span class="absolute left-0 bottom-0 w-0 h-0.5 bg-purple-400 group-hover:w-full transition-all duration-300"></span>
      </a>
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

  <!-- Hero Section -->
  <section class="pt-32 pb-20 px-6 bg-gradient-to-b from-purple-900/10 to-gray-950">
    <div class="max-w-7xl mx-auto text-center">
      <h1 class="text-4xl md:text-5xl font-bold mb-6">About <span class="text-purple-400">Elytra Pool</span></h1>
      <p class="text-xl text-gray-300 max-w-3xl mx-auto">
        Pioneering the future of decentralized finance with secure, transparent, and high-yield staking solutions.
      </p>
    </div>
  </section>

  <!-- Mission Section -->
  <section class="py-16 px-6">
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
      <div class="order-2 md:order-1">
        <h2 class="text-3xl font-bold mb-6 section-title">Our Mission</h2>
        <p class="text-gray-300 mb-6">
          At Elytra Pool, we're committed to democratizing access to cryptocurrency staking by providing an intuitive platform that bridges the gap between traditional finance and decentralized technologies.
        </p>
        <div class="space-y-4">
          <div class="flex items-start">
            <div class="bg-purple-500/10 text-purple-400 rounded-full p-2 mr-4">
              <i class="fas fa-shield-alt"></i>
            </div>
            <div>
              <h4 class="font-semibold mb-1">Security First</h4>
              <p class="text-gray-400 text-sm">Enterprise-grade security protocols to protect your assets</p>
            </div>
          </div>
          <div class="flex items-start">
            <div class="bg-purple-500/10 text-purple-400 rounded-full p-2 mr-4">
              <i class="fas fa-chart-line"></i>
            </div>
            <div>
              <h4 class="font-semibold mb-1">Optimal Yields</h4>
              <p class="text-gray-400 text-sm">Competitive returns through advanced staking strategies</p>
            </div>
          </div>
          <div class="flex items-start">
            <div class="bg-purple-500/10 text-purple-400 rounded-full p-2 mr-4">
              <i class="fas fa-users"></i>
            </div>
            <div>
              <h4 class="font-semibold mb-1">Community Focused</h4>
              <p class="text-gray-400 text-sm">Built by stakers, for stakers</p>
            </div>
          </div>
        </div>
      </div>
      <div class="order-1 md:order-2">
        <div class="bg-gray-800/50 border border-gray-700 rounded-xl p-1">
          <img src="https://images.unsplash.com/photo-1639762681057-408e52192e55?q=80&w=2232&auto=format&fit=crop"
            alt="Blockchain technology"
            class="w-full h-auto rounded-lg">
        </div>
      </div>
    </div>
  </section>

  <!-- Team Section -->
  <section class="pt-20 pb-16 px-6">
    <div class="max-w-7xl mx-auto">
      <div class="text-center mb-16">
        <h2 class="text-3xl font-bold mb-4 section-title inline-block">Meet Our Team</h2>
        <p class="text-gray-400 max-w-2xl mx-auto">The brilliant minds behind Elytra Pool's innovative staking platform</p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Team Member 1 -->
        <div class="card-glass p-6 rounded-2xl text-center team-member fade-in" style="animation-delay: 0.1s;">
          <div class="mb-6 -mt-12">
            <img src="https://randomuser.me/api/portraits/women/44.jpg"
              alt="Alice Johnson"
              class="team-img rounded-full mx-auto">
          </div>
          <h3 class="text-xl font-semibold mb-1">Alice Johnson</h3>
          <p class="text-purple-400 mb-4">CEO & Founder</p>
          <p class="text-gray-300 mb-4">Blockchain expert with 10+ years in cryptocurrency and decentralized systems development.</p>
          <div class="flex justify-center space-x-4">
            <a href="#" class="text-gray-400 hover:text-purple-400 transition"><i class="fab fa-linkedin"></i></a>
            <a href="#" class="text-gray-400 hover:text-purple-400 transition"><i class="fab fa-twitter"></i></a>
            <a href="#" class="text-gray-400 hover:text-purple-400 transition"><i class="fas fa-link"></i></a>
          </div>
        </div>

        <!-- Team Member 2 -->
        <div class="card-glass p-6 rounded-2xl text-center team-member fade-in" style="animation-delay: 0.2s;">
          <div class="mb-6 -mt-12">
            <img src="https://randomuser.me/api/portraits/men/32.jpg"
              alt="Bob Smith"
              class="team-img rounded-full mx-auto">
          </div>
          <h3 class="text-xl font-semibold mb-1">Bob Smith</h3>
          <p class="text-purple-400 mb-4">CTO</p>
          <p class="text-gray-300 mb-4">Software architect specializing in blockchain infrastructure and smart contract security.</p>
          <div class="flex justify-center space-x-4">
            <a href="#" class="text-gray-400 hover:text-purple-400 transition"><i class="fab fa-linkedin"></i></a>
            <a href="#" class="text-gray-400 hover:text-purple-400 transition"><i class="fab fa-github"></i></a>
            <a href="#" class="text-gray-400 hover:text-purple-400 transition"><i class="fab fa-twitter"></i></a>
          </div>
        </div>

        <!-- Team Member 3 -->
        <div class="card-glass p-6 rounded-2xl text-center team-member fade-in" style="animation-delay: 0.3s;">
          <div class="mb-6 -mt-12">
            <img src="https://randomuser.me/api/portraits/men/67.jpg"
              alt="Charlie Brown"
              class="team-img rounded-full mx-auto">
          </div>
          <h3 class="text-xl font-semibold mb-1">Charlie Brown</h3>
          <p class="text-purple-400 mb-4">Marketing Director</p>
          <p class="text-gray-300 mb-4">Growth hacker with expertise in crypto community building and digital marketing strategies.</p>
          <div class="flex justify-center space-x-4">
            <a href="#" class="text-gray-400 hover:text-purple-400 transition"><i class="fab fa-linkedin"></i></a>
            <a href="#" class="text-gray-400 hover:text-purple-400 transition"><i class="fab fa-twitter"></i></a>
            <a href="#" class="text-gray-400 hover:text-purple-400 transition"><i class="fab fa-instagram"></i></a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Stats Section -->
  <section class="pt-16 pb-16 px-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div
          class="p-6 rounded-2xl text-center bg-gradient-to-r from-indigo-500 via-purple-600 to-indigo-700 shadow-md transition-transform duration-300 hover:scale-105">
          <div class="text-3xl font-bold text-white mb-2">$1.2B+</div>
          <div class="text-white-600 text-sm">Assets Staked</div>
        </div>

        <div
          class="p-6 rounded-2xl text-center bg-gradient-to-r from-indigo-500 via-purple-600 to-indigo-700 shadow-md transition-transform duration-400 hover:scale-105">
          <div id="active-users" class="text-3xl font-bold text-white mb-2">1,500,000</div>
          <div class="text-white-600 text-sm">Active Users</div>
        </div>

        <div
          class="p-6 rounded-2xl text-center bg-gradient-to-r from-indigo-500 via-purple-600 to-indigo-700 shadow-md transition-transform duration-300 hover:scale-105">
          <div class="text-3xl font-bold text-white mb-2">15+</div>
          <div class="text-white-600 text-sm">Supported Coins</div>
        </div>

        <div
          class="p-6 rounded-2xl text-center bg-gradient-to-r from-indigo-500 via-purple-600 to-indigo-700 shadow-md transition-transform duration-300 hover:scale-105">
          <div id="uptime" class="text-3xl font-bold text-white mb-2">99.9%</div>
          <div class="text-white-600 text-sm">Uptime</div>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="py-20 px-6 bg-gradient-to-br from-purple-900 to-indigo-900 text-white">
    <div class="max-w-7xl mx-auto text-center">
      <h2 class="text-4xl sm:text-5xl font-extrabold mb-6">Need Help or Want to Estimate Your Rewards?</h2>
      <p class="text-lg sm:text-xl text-blue-100 mb-10 max-w-2xl mx-auto">
        Check out our FAQs for quick answers or use our earnings calculator to estimate your staking income.
      </p>

      <div class="flex flex-col sm:flex-row justify-center gap-4">
        <!-- FAQ Button with background -->
        <a href="faq.php" class="inline-flex items-center justify-center bg-white text-purple-800 px-6 py-3 rounded-lg font-semibold hover:bg-purple-100 transition duration-300 shadow-md text-base sm:text-lg">
          <i class="fas fa-question-circle mr-2"></i>
          Frequently Asked Questions
        </a>

        <!-- Calculate Earnings Button remains bordered white -->
        <a href="index.php#earnings" class="inline-flex items-center justify-center border border-white text-white px-6 py-3 rounded-lg font-semibold hover:bg-white/10 transition duration-300 shadow-md text-base sm:text-lg">
          <i class="fas fa-calculator mr-2"></i>
          Calculate Earnings
        </a>
      </div>
    </div>
  </section>


  <!-- Footer -->
  <footer class="py-12 px-6 border-t border-gray-800">
    <div class="max-w-7xl mx-auto">
      <div class="grid md:grid-cols-4 gap-8 mb-8">
        <div class="flex items-center space-x-2">
          <a href="index.php" class="flex items-center">
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
            <li><a href="faq.php" class="hover:text-white">FAQ</a></li>
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

  <script>
    // Simple animation trigger
    document.addEventListener('DOMContentLoaded', () => {
      const fadeElements = document.querySelectorAll('.fade-in');

      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.style.opacity = 1;
          }
        });
      }, {
        threshold: 0.1
      });

      fadeElements.forEach(el => observer.observe(el));
    });
  </script>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const menuBtn = document.getElementById("mobile-menu-button");
      const mobileMenu = document.getElementById("mobile-menu");

      menuBtn?.addEventListener("click", () => {
        mobileMenu.classList.toggle("hidden");
      });
    });
  </script>

  <script>
    function showToast(message, type = 'success') {
      const toast = document.createElement('div');
      toast.className = `
      px-4 py-3 rounded-md shadow-md text-white text-sm transition-opacity duration-500 animate-slide-in-right
      ${type === 'success' ? 'bg-green-600' : 'bg-red-600'}
    `;
      toast.textContent = message;

      document.getElementById('toastContainer').appendChild(toast);

      setTimeout(() => {
        toast.classList.add('opacity-0');
        setTimeout(() => toast.remove(), 500);
      }, 3000); // show for 3 seconds
    }
  </script>

  <script>
    const loginModal = document.getElementById("loginModal");
    const closeModal = document.getElementById("closeModal");
    const switchToSignUp = document.getElementById("switchToSignUp");
    const loginForm = document.getElementById("loginForm");

    document.getElementById("login-button").addEventListener("click", () => {
      loginForm.classList.remove("hidden");
      loginModal.classList.remove("hidden");
      setTimeout(() => {
        loginModal.querySelector("#modalContent").classList.remove("scale-95", "opacity-0");
        loginModal.querySelector("#modalContent").classList.add("scale-100", "opacity-100");
      }, 10);
    });

    closeModal.addEventListener("click", () => {
      loginModal.querySelector("#modalContent").classList.add("scale-95", "opacity-0");
      setTimeout(() => {
        loginModal.classList.add("hidden");
      }, 300);
    });

    switchToSignUp.addEventListener("click", () => {
      loginForm.classList.add("hidden");
      document.getElementById("signUpForm").classList.remove("hidden");
    });

    window.addEventListener("click", (e) => {
      if (e.target === loginModal) closeModal.click();
    });

    const forgotPasswordBtn = document.getElementById("forgotPasswordBtn");
    const forgotPasswordModal = document.getElementById("forgotPasswordModal");
    const forgotPasswordContent = document.getElementById("forgotPasswordContent");
    const cancelForgot = document.getElementById("cancelForgot");
    const forgotPasswordForm = document.getElementById("forgotPasswordForm");

    forgotPasswordBtn.addEventListener("click", (e) => {
      e.preventDefault();
      forgotPasswordModal.classList.remove("hidden");
      setTimeout(() => {
        forgotPasswordContent.classList.remove("scale-95", "opacity-0");
        forgotPasswordContent.classList.add("scale-100", "opacity-100");
      }, 10);
    });

    cancelForgot.addEventListener("click", () => {
      forgotPasswordContent.classList.add("scale-95", "opacity-0");
      setTimeout(() => {
        forgotPasswordModal.classList.add("hidden");
      }, 300);
    });

    forgotPasswordForm.addEventListener("submit", (e) => {
      e.preventDefault();
      const email = document.getElementById("forgotEmail").value;
      alert("Reset link sent to: " + email);
      cancelForgot.click();
    });
  </script>

  <script>
    const signUpForm = document.getElementById("signUpForm");
    const switchToLogin = document.getElementById("switchToLogin");
    const infoModal = document.getElementById("infoModal");
    const privacyModal = document.getElementById("privacyModal");
    const confirmTerms = document.getElementById("confirmTerms");
    const confirmPrivacy = document.getElementById("confirmPrivacy");
    const termsContent = document.getElementById("termsContent");
    const privacyContent = document.getElementById("privacyContent");
    const agreeTerms = document.getElementById("agreeTerms");
    const agreePrivacy = document.getElementById("agreePrivacy");

    const signUpBtn = signUpForm.querySelector('button[type="submit"]');

    function updateSubmitState() {
      const bothChecked = agreeTerms.checked && agreePrivacy.checked;
      signUpBtn.disabled = !bothChecked;
      signUpBtn.classList.toggle("opacity-50", !bothChecked);
      signUpBtn.classList.toggle("cursor-not-allowed", !bothChecked);
      signUpBtn.classList.toggle("cursor-pointer", bothChecked);
    }

    agreeTerms.addEventListener("change", updateSubmitState);
    agreePrivacy.addEventListener("change", updateSubmitState);

    // Initial state
    updateSubmitState();

    let termsConfirmed = false;
    let privacyConfirmed = false;

    switchToLogin.addEventListener("click", () => {
      signUpForm.classList.add("hidden");
      document.getElementById("loginForm").classList.remove("hidden");
    });

    agreeTerms.addEventListener("mousedown", function(e) {
      if (!termsConfirmed && !this.checked) {
        e.preventDefault();
        infoModal.classList.remove("hidden");
        setTimeout(() => {
          infoModal.querySelector("#infoModalContent").classList.remove("scale-95", "opacity-0");
          infoModal.querySelector("#infoModalContent").classList.add("scale-100", "opacity-100");
        }, 10);
      }
    });

    agreePrivacy.addEventListener("mousedown", function(e) {
      if (!privacyConfirmed && !this.checked) {
        e.preventDefault();
        privacyModal.classList.remove("hidden");
        setTimeout(() => {
          privacyModal.querySelector("#privacyModalContent").classList.remove("scale-95", "opacity-0");
          privacyModal.querySelector("#privacyModalContent").classList.add("scale-100", "opacity-100");
        }, 10);
      }
    });

    confirmTerms.addEventListener("click", () => {
      termsConfirmed = true;
      agreeTerms.checked = true;
      updateSubmitState(); // ✅ This line enables the button
      infoModal.querySelector("#infoModalContent").classList.add("scale-95", "opacity-0");
      setTimeout(() => {
        infoModal.classList.add("hidden");
      }, 300);
    });

    confirmPrivacy.addEventListener("click", () => {
      privacyConfirmed = true;
      agreePrivacy.checked = true;
      updateSubmitState(); // ✅ This line enables the button
      privacyModal.querySelector("#privacyModalContent").classList.add("scale-95", "opacity-0");
      setTimeout(() => {
        privacyModal.classList.add("hidden");
      }, 300);
    });

    termsContent.addEventListener("scroll", () => {
      const scrolled = termsContent.scrollTop + termsContent.clientHeight >= termsContent.scrollHeight;
      confirmTerms.disabled = !scrolled;
      confirmTerms.classList.toggle("bg-blue-600", scrolled);
      confirmTerms.classList.toggle("hover:bg-blue-700", scrolled);
      confirmTerms.classList.toggle("cursor-pointer", scrolled);
      confirmTerms.classList.toggle("bg-gray-400", !scrolled);
      confirmTerms.classList.toggle("cursor-not-allowed", !scrolled);
    });

    privacyContent.addEventListener("scroll", () => {
      const scrolled = privacyContent.scrollTop + privacyContent.clientHeight >= privacyContent.scrollHeight;
      confirmPrivacy.disabled = !scrolled;
      confirmPrivacy.classList.toggle("bg-blue-600", scrolled);
      confirmPrivacy.classList.toggle("hover:bg-blue-700", scrolled);
      confirmPrivacy.classList.toggle("cursor-pointer", scrolled);
      confirmPrivacy.classList.toggle("bg-gray-400", !scrolled);
      confirmPrivacy.classList.toggle("cursor-not-allowed", !scrolled);
    });

    window.addEventListener("click", (e) => {
      if (e.target === infoModal) {
        infoModal.querySelector("#infoModalContent").classList.add("scale-95", "opacity-0");
        setTimeout(() => infoModal.classList.add("hidden"), 300);
      }
      if (e.target === privacyModal) {
        privacyModal.querySelector("#privacyModalContent").classList.add("scale-95", "opacity-0");
        setTimeout(() => privacyModal.classList.add("hidden"), 300);
      }
    });

    signUpForm.querySelector("form").addEventListener("submit", async (e) => {
      e.preventDefault();

      const form = e.target;
      const formData = new FormData(form);

      // Required checkbox validation
      if (!agreeTerms.checked || !agreePrivacy.checked) {
        showToast("❌ You must agree to both Terms and Privacy Policy.", 'error');
        return;
      }

      try {
        const response = await fetch("config/registration.php", {
          method: "POST",
          body: formData
        });

        const result = await response.json();

        if (result.status === "success") {
          showToast("✅ " + result.message, 'success');
          form.reset();
          signUpForm.classList.add("hidden");
          document.getElementById("loginForm").classList.remove("hidden");
        } else {
          showToast("❌ " + result.message, 'error');
        }
      } catch (error) {
        console.error("Error:", error);
        showToast("❌ An error occurred while registering.", 'error');

      }
    });

    function showToast(message, type = 'success') {
      const toast = document.createElement('div');
      toast.className = `
    px-4 py-3 rounded-md shadow-md text-white text-sm animate-slide-in-right
    ${type === 'success' ? 'bg-green-600' : 'bg-red-600'}
  `;
      toast.textContent = message;

      document.getElementById('toastContainer').appendChild(toast);

      setTimeout(() => {
        toast.classList.add('opacity-0');
        setTimeout(() => toast.remove(), 500);
      }, 3000);
    }

    // Password strength validation
    const signUpPassword = document.getElementById("signUpPassword");
    const passwordStrengthText = document.createElement("p");
    passwordStrengthText.classList.add("mt-1", "text-sm", "font-semibold");
    signUpPassword.parentNode.appendChild(passwordStrengthText);

    signUpPassword.addEventListener("input", () => {
      const password = signUpPassword.value;
      const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);
      let strength = '';
      let strengthColor = '';

      if (password.length < 6) {
        strength = 'Weak';
        strengthColor = 'text-red-600';
      } else if (password.length >= 6 && password.length < 10) {
        strength = 'Medium';
        strengthColor = 'text-yellow-600';
      } else if (password.length >= 10 && /[A-Z]/.test(password) && /[0-9]/.test(password) && hasSpecialChar) {
        strength = 'Strong';
        strengthColor = 'text-green-600';
      } else {
        strength = 'Medium';
        strengthColor = 'text-yellow-600';
      }

      if (!hasSpecialChar) {
        strength = 'Must include at least one special character';
        strengthColor = 'text-red-600';
      }

      passwordStrengthText.textContent = strength;
      passwordStrengthText.className = `mt-1 text-sm font-semibold ${strengthColor}`;
    });
  </script>

  <script>
    window.onload = function() {
      const savedEmail = localStorage.getItem("rememberedEmail");
      const remember = localStorage.getItem("rememberMeChecked");
      if (savedEmail && remember === "true") {
        document.getElementById("email").value = savedEmail;
        document.getElementById("rememberMe").checked = true;
      }
    };

    document.querySelector("form").addEventListener("submit", () => {
      const remember = document.getElementById("rememberMe").checked;
      const email = document.getElementById("email").value;
      if (remember) {
        localStorage.setItem("rememberedEmail", email);
        localStorage.setItem("rememberMeChecked", "true");
      } else {
        localStorage.removeItem("rememberedEmail");
        localStorage.removeItem("rememberMeChecked");
      }
    });
  </script>

  <script>
    // Connect buttons to the login modal
    document.addEventListener("DOMContentLoaded", function() {
      const loginModal = document.getElementById("loginModal");
      const closeModal = document.getElementById("closeModal");

      // Function to show the login modal
      function showLoginModal() {
        loginModal.classList.remove("hidden");
        setTimeout(() => {
          loginModal.querySelector("#modalContent").classList.remove("scale-95", "opacity-0");
          loginModal.querySelector("#modalContent").classList.add("scale-100", "opacity-100");
        }, 10);
      }

      // Connect "Start Earning" button
      document.getElementById("start-earning-button").addEventListener("click", showLoginModal);

      // Connect "Stake Now" buttons
      document.querySelectorAll("button[id$='-stake-button']").forEach(button => {
        button.addEventListener("click", showLoginModal);
      });

      // Connect "Get Started Now" button
      document.getElementById("login-button-modal").addEventListener("click", showLoginModal);

      // Close modal functionality
      closeModal.addEventListener("click", () => {
        loginModal.querySelector("#modalContent").classList.add("scale-95", "opacity-0");
        setTimeout(() => {
          loginModal.classList.add("hidden");
        }, 300);
      });
    });
  </script>

  <script src="assets/js/script.js"></script>
  <script src="assets/js/randomizer.js"></script>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <script src="assets/js/calculator.js"></script>
  <div id="toastContainer" class="fixed top-4 right-4 z-50 space-y-2"></div>
</body>

</html>