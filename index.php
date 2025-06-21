<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elytra Pool | Staking & Mining Platform</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body class="min-h-screen">
    <!-- Navigation -->
    <nav class="gradient-bg border-b border-gray-800 py-4 px-6 fixed w-full z-50">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center pulse">
                    <i class="fas fa-coins text-white"></i>
                </div>
                <span class="text-xl font-bold">Elytra Pool</span>
            </div>

            <div class="hidden md:flex space-x-8">
                <a href="#home" class="nav-link">Home</a>
                <a href="#staking" class="nav-link">Staking</a>
                <a href="#mining" class="nav-link">Mining</a>
                <a href="#earnings" class="nav-link">Earnings</a>
                <a href="#about" class="nav-link">About</a>
            </div>

            <div class="flex items-center space-x-4">
                <button class="btn-secondary px-4 py-2 rounded-lg">Log In</button>
                <button class="btn-primary px-4 py-2 rounded-lg">Sign Up</button>
            </div>

            <button class="md:hidden">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="pt-32 pb-20 px-6">
        <div class="max-w-7xl mx-auto grid md:grid-cols-2 gap-12 items-center">
            <div class="slide-in-left">
                <h1 class="text-4xl md:text-5xl font-bold mb-6">Earn Passive Crypto <span class="text-blue-400">Effortlessly</span></h1>
                <p class="text-lg text-gray-300 mb-8">Join thousands of investors earning up to 25% APY through our secure staking and cloud mining platform.</p>
                <div class="flex space-x-4">
                    <button class="btn-primary px-6 py-3 rounded-lg">Start Earning</button>
                    <button class="btn-secondary px-6 py-3 rounded-lg">How It Works</button>
                </div>
                <div class="mt-12 flex flex-wrap gap-4">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-400 rounded-full mr-2"></div>
                        <span>Secure Platform</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-blue-400 rounded-full mr-2"></div>
                        <span>Instant Withdrawals</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-purple-400 rounded-full mr-2"></div>
                        <span>24/7 Support</span>
                    </div>
                </div>
            </div>

            <div class="relative slide-in-right">
                <div class="card-glass p-6 rounded-2xl shadow-xl mining-animation">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-semibold">Your Mining Dashboard</h3>
                        <div class="text-green-400 font-medium">Active</div>
                    </div>

                    <div class="h-64 mb-6">
                        <canvas id="miningChart"></canvas>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <div class="text-sm text-gray-400">Hashrate</div>
                            <div class="text-xl font-semibold">1.24 TH/s</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-400">Daily Earnings</div>
                            <div class="text-xl font-semibold">0.0042 BTC</div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="flex justify-between text-sm mb-1">
                            <span>Mining Progress</span>
                            <span>78%</span>
                        </div>
                        <div class="w-full bg-gray-800 rounded-full h-2">
                            <div class="progress-bar" style="width: 78%"></div>
                        </div>
                    </div>
                </div>

                <div class="absolute -top-6 -left-6 w-32 h-32 bg-blue-500 rounded-full opacity-10 floating"></div>
                <div class="absolute -bottom-6 -right-6 w-24 h-24 bg-purple-500 rounded-full opacity-10 floating" style="animation-delay: 0.5s;"></div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-12 bg-gray-900">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="card-glass p-6 rounded-2xl text-center fade-in" style="animation-delay: 0.1s;">
                    <div class="text-3xl font-bold mb-2">$1.2B+</div>
                    <div class="text-gray-400">Assets Staked</div>
                </div>
                <div class="card-glass p-6 rounded-2xl text-center fade-in" style="animation-delay: 0.2s;">
                    <div class="text-3xl font-bold mb-2">250K+</div>
                    <div class="text-gray-400">Active Users</div>
                </div>
                <div class="card-glass p-6 rounded-2xl text-center fade-in" style="animation-delay: 0.3s;">
                    <div class="text-3xl font-bold mb-2">15+</div>
                    <div class="text-gray-400">Supported Coins</div>
                </div>
                <div class="card-glass p-6 rounded-2xl text-center fade-in" style="animation-delay: 0.4s;">
                    <div class="text-3xl font-bold mb-2">99.9%</div>
                    <div class="text-gray-400">Uptime</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Staking Section -->
    <section id="staking" class="py-20 px-6">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl font-bold mb-2 text-center">Crypto Staking</h2>
            <p class="text-gray-400 mb-12 text-center max-w-2xl mx-auto">Earn passive income by staking your cryptocurrencies with our secure platform.</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Bitcoin Staking -->
                <div class="staking-card p-6 rounded-2xl cursor-pointer fade-in" style="animation-delay: 0.1s;">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-orange-500 rounded-full flex items-center justify-center mr-3">
                                <i class="fab fa-btc text-white"></i>
                            </div>
                            <div>
                                <div class="font-semibold">Bitcoin</div>
                                <div class="text-sm text-gray-400">BTC Staking</div>
                            </div>
                        </div>
                        <div class="bg-blue-500/10 text-blue-400 px-3 py-1 rounded-full text-sm">6.5% APY</div>
                    </div>
                    <div class="mb-6">
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-400">Total Staked</span>
                            <span>12,450 BTC</span>
                        </div>
                        <div class="w-full bg-gray-800 rounded-full h-2">
                            <div class="progress-bar" style="width: 65%"></div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <div class="text-sm text-gray-400">Min. Stake</div>
                            <div class="font-semibold">0.01 BTC</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-400">Lock Period</div>
                            <div class="font-semibold">30 Days</div>
                        </div>
                    </div>
                    <button class="btn-primary w-full py-2 rounded-lg">Stake Now</button>
                </div>

                <!-- Ethereum Staking -->
                <div class="staking-card p-6 rounded-2xl cursor-pointer fade-in" style="animation-delay: 0.2s;">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gray-400 rounded-full flex items-center justify-center mr-3">
                                <i class="fab fa-ethereum text-white"></i>
                            </div>
                            <div>
                                <div class="font-semibold">Ethereum</div>
                                <div class="text-sm text-gray-400">ETH Staking</div>
                            </div>
                        </div>
                        <div class="bg-blue-500/10 text-blue-400 px-3 py-1 rounded-full text-sm">5.2% APY</div>
                    </div>
                    <div class="mb-6">
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-400">Total Staked</span>
                            <span>245,000 ETH</span>
                        </div>
                        <div class="w-full bg-gray-800 rounded-full h-2">
                            <div class="progress-bar" style="width: 78%"></div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <div class="text-sm text-gray-400">Min. Stake</div>
                            <div class="font-semibold">0.1 ETH</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-400">Lock Period</div>
                            <div class="font-semibold">14 Days</div>
                        </div>
                    </div>
                    <button class="btn-primary w-full py-2 rounded-lg">Stake Now</button>
                </div>

                <!-- Solana Staking -->
                <div class="staking-card p-6 rounded-2xl cursor-pointer fade-in" style="animation-delay: 0.3s;">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-purple-500 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-bolt text-white"></i>
                            </div>
                            <div>
                                <div class="font-semibold">Solana</div>
                                <div class="text-sm text-gray-400">SOL Staking</div>
                            </div>
                        </div>
                        <div class="bg-blue-500/10 text-blue-400 px-3 py-1 rounded-full text-sm">8.7% APY</div>
                    </div>
                    <div class="mb-6">
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-400">Total Staked</span>
                            <span>4,560,000 SOL</span>
                        </div>
                        <div class="w-full bg-gray-800 rounded-full h-2">
                            <div class="progress-bar" style="width: 45%"></div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <div class="text-sm text-gray-400">Min. Stake</div>
                            <div class="font-semibold">1 SOL</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-400">Lock Period</div>
                            <div class="font-semibold">7 Days</div>
                        </div>
                    </div>
                    <button class="btn-primary w-full py-2 rounded-lg">Stake Now</button>
                </div>
            </div>
            <div class="mt-12 text-center">
                <button class="btn-secondary px-6 py-3 rounded-lg">View All Staking Options</button>
            </div>
        </div>
    </section>

    <!-- Mining Section -->
    <section id="mining" class="py-20 px-6 bg-gray-900">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl font-bold mb-2 text-center">Cloud Mining</h2>
            <p class="text-gray-400 mb-12 text-center max-w-2xl mx-auto">Start mining cryptocurrency without hardware or technical knowledge.</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Starter Plan -->
                <div class="mining-card p-6 rounded-2xl cursor-pointer fade-in" style="animation-delay: 0.1s;">
                    <div class="text-center mb-6">
                        <h3 class="text-xl font-semibold">Starter</h3>
                        <div class="text-3xl font-bold mt-2">$299</div>
                        <div class="text-gray-400">per month</div>
                    </div>
                    <div class="space-y-4 mb-6">
                        <div class="flex items-center">
                            <i class="fas fa-check text-green-400 mr-2"></i>
                            <span>100 GH/s Hashrate</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check text-green-400 mr-2"></i>
                            <span>Daily Payouts</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check text-green-400 mr-2"></i>
                            <span>0.5% Pool Fee</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check text-green-400 mr-2"></i>
                            <span>24/7 Monitoring</span>
                        </div>
                    </div>
                    <div class="text-center mb-6">
                        <div class="text-sm text-gray-400 mb-1">Estimated Monthly Earnings</div>
                        <div class="text-xl font-bold">0.0012 BTC</div>
                    </div>
                    <button class="btn-primary w-full py-2 rounded-lg">Start Mining</button>
                </div>

                <!-- Professional Plan -->
                <div class="mining-card p-6 rounded-2xl cursor-pointer fade-in" style="animation-delay: 0.2s; border: 2px solid #3b82f6;">
                    <div class="absolute top-0 right-0 bg-blue-500 text-white text-xs font-bold px-3 py-1 rounded-bl rounded-tr">POPULAR</div>
                    <div class="text-center mb-6">
                        <h3 class="text-xl font-semibold">Professional</h3>
                        <div class="text-3xl font-bold mt-2">$999</div>
                        <div class="text-gray-400">per month</div>
                    </div>
                    <div class="space-y-4 mb-6">
                        <div class="flex items-center">
                            <i class="fas fa-check text-green-400 mr-2"></i>
                            <span>1 TH/s Hashrate</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check text-green-400 mr-2"></i>
                            <span>Daily Payouts</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check text-green-400 mr-2"></i>
                            <span>0% Pool Fee</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check text-green-400 mr-2"></i>
                            <span>24/7 Monitoring</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check text-green-400 mr-2"></i>
                            <span>Dedicated Support</span>
                        </div>
                    </div>
                    <div class="text-center mb-6">
                        <div class="text-sm text-gray-400 mb-1">Estimated Monthly Earnings</div>
                        <div class="text-xl font-bold">0.0042 BTC</div>
                    </div>
                    <button class="btn-primary w-full py-2 rounded-lg">Start Mining</button>
                </div>

                <!-- Enterprise Plan -->
                <div class="mining-card p-6 rounded-2xl cursor-pointer fade-in" style="animation-delay: 0.3s;">
                    <div class="text-center mb-6">
                        <h3 class="text-xl font-semibold">Enterprise</h3>
                        <div class="text-3xl font-bold mt-2">$2,999</div>
                        <div class="text-gray-400">per month</div>
                    </div>
                    <div class="space-y-4 mb-6">
                        <div class="flex items-center">
                            <i class="fas fa-check text-green-400 mr-2"></i>
                            <span>5 TH/s Hashrate</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check text-green-400 mr-2"></i>
                            <span>Hourly Payouts</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check text-green-400 mr-2"></i>
                            <span>0% Pool Fee</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check text-green-400 mr-2"></i>
                            <span>24/7 Monitoring</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check text-green-400 mr-2"></i>
                            <span>Dedicated Support</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check text-green-400 mr-2"></i>
                            <span>Custom Dashboard</span>
                        </div>
                    </div>
                    <div class="text-center mb-6">
                        <div class="text-sm text-gray-400 mb-1">Estimated Monthly Earnings</div>
                        <div class="text-xl font-bold">0.021 BTC</div>
                    </div>
                    <button class="btn-primary w-full py-2 rounded-lg">Start Mining</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Earnings Calculator -->
    <section id="earnings" class="py-20 px-6">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl font-bold mb-2 text-center">Earnings Calculator</h2>
            <p class="text-gray-400 mb-12 text-center max-w-2xl mx-auto">Estimate your potential earnings from staking or mining.</p>

            <div class="grid lg:grid-cols-2 gap-12">
                <div class="card-glass p-8 rounded-2xl slide-in-left">
                    <h3 class="text-2xl font-semibold mb-4">Staking Calculator</h3>

                    <div class="mb-6">
                        <label class="block text-sm text-gray-400 mb-2">Cryptocurrency</label>
                        <select class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2">
                            <option>Bitcoin (BTC)</option>
                            <option>Ethereum (ETH)</option>
                            <option>Solana (SOL)</option>
                            <option>Cardano (ADA)</option>
                            <option>Polkadot (DOT)</option>
                        </select>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm text-gray-400 mb-2">Amount to Stake</label>
                        <div class="flex">
                            <input type="number" class="w-full bg-gray-800 border border-gray-700 rounded-l-lg px-4 py-2" value="1.0">
                            <div class="bg-gray-700 px-4 py-2 rounded-r-lg">BTC</div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm text-gray-400 mb-2">Staking Period</label>
                        <select class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2">
                            <option>7 Days</option>
                            <option>14 Days</option>
                            <option>30 Days</option>
                            <option>60 Days</option>
                            <option>90 Days</option>
                        </select>
                    </div>

                    <button class="btn-primary w-full py-3 rounded-lg mb-4">Calculate Earnings</button>

                    <div class="card-glass p-4 rounded-lg">
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-400">APY</span>
                            <span class="font-semibold">6.5%</span>
                        </div>
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-400">Estimated Rewards</span>
                            <span class="font-semibold">0.065 BTC</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Total Return</span>
                            <span class="font-semibold">1.065 BTC</span>
                        </div>
                    </div>
                </div>

                <div class="card-glass p-8 rounded-2xl slide-in-right">
                    <h3 class="text-2xl font-semibold mb-4">Mining Calculator</h3>

                    <div class="mb-6">
                        <label class="block text-sm text-gray-400 mb-2">Mining Plan</label>
                        <select class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2">
                            <option>Starter (100 GH/s)</option>
                            <option>Professional (1 TH/s)</option>
                            <option>Enterprise (5 TH/s)</option>
                        </select>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm text-gray-400 mb-2">Mining Period</label>
                        <select class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2">
                            <option>1 Month</option>
                            <option>3 Months</option>
                            <option>6 Months</option>
                            <option>1 Year</option>
                        </select>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm text-gray-400 mb-2">Electricity Cost ($/kWh)</label>
                        <input type="number" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2" value="0.12" step="0.01">
                    </div>

                    <button class="btn-primary w-full py-3 rounded-lg mb-4">Calculate Earnings</button>

                    <div class="card-glass p-4 rounded-lg">
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-400">Hashrate</span>
                            <span class="font-semibold">1 TH/s</span>
                        </div>
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-400">Estimated Monthly Rewards</span>
                            <span class="font-semibold">0.0042 BTC</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Profit After Costs</span>
                            <span class="font-semibold text-green-400">$138.60</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="py-20 px-6 bg-gray-900">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl font-bold mb-2 text-center">How It Works</h2>
            <p class="text-gray-400 mb-12 text-center max-w-2xl mx-auto">Start earning passive income in just a few simple steps.</p>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center fade-in" style="animation-delay: 0.1s;">
                    <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-white text-2xl font-bold">1</span>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Create Account</h3>
                    <p class="text-gray-400">Sign up and verify your identity to access our staking and mining platform.</p>
                </div>

                <div class="text-center fade-in" style="animation-delay: 0.2s;">
                    <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-white text-2xl font-bold">2</span>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Deposit Funds</h3>
                    <p class="text-gray-400">Transfer your crypto to your Elytra Pool wallet or purchase a mining contract.</p>
                </div>

                <div class="text-center fade-in" style="animation-delay: 0.3s;">
                    <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-white text-2xl font-bold">3</span>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Start Earning</h3>
                    <p class="text-gray-400">Stake your coins or activate mining to begin earning passive income.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="py-20 px-6">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl font-bold mb-2 text-center">What Our Users Say</h2>
            <p class="text-gray-400 mb-12 text-center max-w-2xl mx-auto">Don't just take our word for it. Here's what our community has to say.</p>

            <div class="grid md:grid-cols-3 gap-6">
                <div class="card-glass p-6 rounded-2xl fade-in" style="animation-delay: 0.1s;">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-gray-700 rounded-full mr-4"></div>
                        <div>
                            <div class="font-semibold">Michael T.</div>
                            <div class="text-sm text-gray-400">Professional Miner</div>
                        </div>
                    </div>
                    <p class="text-gray-300">"The enterprise mining plan has increased my monthly profits by 40% compared to my previous provider. The dedicated support is exceptional."</p>
                    <div class="flex mt-4">
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                    </div>
                </div>

                <div class="card-glass p-6 rounded-2xl fade-in" style="animation-delay: 0.2s;">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-gray-700 rounded-full mr-4"></div>
                        <div>
                            <div class="font-semibold">Sarah K.</div>
                            <div class="text-sm text-gray-400">Long-term Investor</div>
                        </div>
                    </div>
                    <p class="text-gray-300">"I've been staking my Bitcoin and Ethereum with Elytra Pool for over a year now. The returns are consistent and withdrawals are always instant."</p>
                    <div class="flex mt-4">
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                    </div>
                </div>

                <div class="card-glass p-6 rounded-2xl fade-in" style="animation-delay: 0.3s;">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-gray-700 rounded-full mr-4"></div>
                        <div>
                            <div class="font-semibold">David P.</div>
                            <div class="text-sm text-gray-400">New to Crypto</div>
                        </div>
                    </div>
                    <p class="text-gray-300">"As someone new to crypto, Elytra Pool made staking incredibly simple. The dashboard is intuitive and I'm earning rewards with minimal effort."</p>
                    <div class="flex mt-4">
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-20 px-6 bg-gradient-to-r from-blue-600 to-purple-600">
        <div class="max-w-7xl mx-auto text-center">
            <h2 class="text-3xl font-bold mb-4">Ready to Start Earning with Elytra Pool?</h2>
            <p class="text-xl text-blue-100 mb-8 max-w-3xl mx-auto">Join thousands of investors earning passive income through our secure staking and mining platform.</p>
            <button class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-blue-50 transition">Get Started Now</button>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-12 px-6 border-t border-gray-800">
        <div class="max-w-7xl mx-auto">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-coins text-white"></i>
                        </div>
                        <span class="text-lg font-bold">Elytra Pool</span>
                    </div>
                    <p class="text-gray-400 text-sm">The most advanced cryptocurrency staking and mining platform.</p>
                </div>

                <div>
                    <h4 class="font-semibold mb-4">Products</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="#staking" class="hover:text-white">Staking</a></li>
                        <li><a href="#mining" class="hover:text-white">Mining</a></li>
                        <li><a href="#earnings" class="hover:text-white">Earnings Calculator</a></li>
                        <li><a href="#about" class="hover:text-white">About Us</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold mb-4">Support</h4>
                    <ul class="space-y-极速 text-sm text-gray-400">
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
                        <li><a href="#" class="hover:text-white">GitHub</a></li>
                    </ul>
                </div>
            </div>

            <div class="pt-8 border-t border-gray-800 flex flex-col md:flex-row justify-between items-center">
                <div class="text-sm text-gray-400 mb-4 md:mb-0">© 2023 Elytra Pool. All rights reserved.</div>
                <div class="flex space-x-6">
                    <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-telegram"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white">
                        <i class="fab fa-discord"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-github"></i></a>
                </div>
            </div>
        </div>
    </footer>
    
    <script src="assets/js/script.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>

</html>