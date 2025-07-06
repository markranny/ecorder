<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

function handleImageError() {
    document.getElementById('screenshot-container')?.classList.add('!hidden');
    document.getElementById('docs-card')?.classList.add('!row-span-1');
    document.getElementById('docs-card-content')?.classList.add('!flex-row');
    document.getElementById('background')?.classList.add('!hidden');
}
</script>

<template>
    <Head title="Welcome">
        <meta name="theme-color" content="#000000" />
        <link rel="manifest" href="/manifest.json" />
        <link rel="apple-touch-icon" href="/images/icons/icon-192x192.png">
        <meta name="apple-mobile-web-app-status-bar" content="#000000" />
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="mobile-web-app-capable" content="yes" />
    </Head>
    
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-slate-100 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900">
        <!-- Animated Background Elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-gradient-to-br from-blue-400/20 to-purple-600/20 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-gradient-to-tr from-emerald-400/20 to-blue-600/20 rounded-full blur-3xl animate-pulse delay-1000"></div>
        </div>

        <!-- Glass morphism overlay -->
        <div class="absolute inset-0 bg-white/10 dark:bg-black/10 backdrop-blur-[1px]"></div>

        <div class="relative min-h-screen flex flex-col">
            <!-- Header -->
            <header class="relative z-10 px-6 py-8">
                <div class="max-w-7xl mx-auto">
                    <div class="flex items-center justify-between">
                        <!-- Logo Section -->
                        <div class="flex items-center space-x-4">
                            <div class="relative">
                                <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl blur opacity-75"></div>
                                <div class="relative bg-gradient-to-r from-blue-600 to-purple-600 p-3 rounded-2xl">
                                    <svg class="h-8 w-8 text-white" viewBox="0 0 62 65" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M61.8548 14.6253C61.8778 14.7102 61.8895 14.7978 61.8897 14.8858V28.5615C61.8898 28.737 61.8434 28.9095 61.7554 29.0614C61.6675 29.2132 61.5409 29.3392 61.3887 29.4265L49.9104 36.0351V49.1337C49.9104 49.4902 49.7209 49.8192 49.4118 49.9987L25.4519 63.7916C25.3971 63.8227 25.3372 63.8427 25.2774 63.8639C25.255 63.8714 25.2338 63.8851 25.2101 63.8913C25.0426 63.9354 24.8666 63.9354 24.6991 63.8913C24.6716 63.8838 24.6467 63.8689 24.6205 63.8589C24.5657 63.8389 24.5084 63.8215 24.456 63.7916L0.501061 49.9987C0.348882 49.9113 0.222437 49.7853 0.134469 49.6334C0.0465019 49.4816 0.000120578 49.3092 0 49.1337L0 8.10652C0 8.01678 0.0124642 7.92953 0.0348998 7.84477C0.0423783 7.8161 0.0598282 7.78993 0.0697995 7.76126C0.0884958 7.70891 0.105946 7.65531 0.133367 7.6067C0.152063 7.5743 0.179485 7.54812 0.20192 7.51821C0.230588 7.47832 0.256763 7.43719 0.290416 7.40229C0.319084 7.37362 0.356476 7.35243 0.388883 7.32751C0.425029 7.29759 0.457436 7.26518 0.498568 7.2415L12.4779 0.345059C12.6296 0.257786 12.8015 0.211853 12.9765 0.211853C13.1515 0.211853 13.3234 0.257786 13.475 0.345059L25.4531 7.2415H25.4556C25.4955 7.26643 25.5292 7.29759 25.5653 7.32626C25.5977 7.35119 25.6339 7.37362 25.6625 7.40104C25.6974 7.43719 25.7224 7.47832 25.7523 7.51821C25.7735 7.54812 25.8021 7.5743 25.8196 7.6067C25.8483 7.65656 25.8645 7.70891 25.8844 7.76126C25.8944 7.78993 25.9118 7.8161 25.9193 7.84602C25.9423 7.93096 25.954 8.01853 25.9542 8.10652V33.7317L35.9355 27.9844V14.8846C35.9355 14.7973 35.948 14.7088 35.9704 14.6253C35.9792 14.5954 35.9954 14.5692 36.0053 14.5405C36.0253 14.4882 36.0427 14.4346 36.0702 14.386C36.0888 14.3536 36.1163 14.3274 36.1375 14.2975C36.1674 14.2576 36.1923 14.2165 36.2272 14.1816C36.2559 14.1529 36.292 14.1317 36.3244 14.1068C36.3618 14.0769 36.3942 14.0445 36.4341 14.0208L48.4147 7.12434C48.5663 7.03694 48.7383 6.99094 48.9133 6.99094C49.0883 6.99094 49.2602 7.03694 49.4118 7.12434L61.3899 14.0208C61.4323 14.0457 61.4647 14.0769 61.5021 14.1055C61.5333 14.1305 61.5694 14.1529 61.5981 14.1803C61.633 14.2165 61.6579 14.2576 61.6878 14.2975C61.7103 14.3274 61.7377 14.3536 61.7551 14.386C61.7838 14.4346 61.8 14.4882 61.8199 14.5405C61.8312 14.5692 61.8474 14.5954 61.8548 14.6253ZM59.893 27.9844V16.6121L55.7013 19.0252L49.9104 22.3593V33.7317L59.8942 27.9844H59.893ZM47.9149 48.5566V37.1768L42.2187 40.4299L25.953 49.7133V61.2003L47.9149 48.5566ZM1.99677 9.83281V48.5566L23.9562 61.199V49.7145L12.4841 43.2219L12.4804 43.2194L12.4754 43.2169C12.4368 43.1945 12.4044 43.1621 12.3682 43.1347C12.3371 43.1097 12.3009 43.0898 12.2735 43.0624L12.271 43.0586C12.2386 43.0275 12.2162 42.9888 12.1887 42.9539C12.1638 42.9203 12.1339 42.8916 12.114 42.8567L12.1127 42.853C12.0903 42.8156 12.0766 42.7707 12.0604 42.7283C12.0442 42.6909 12.023 42.656 12.013 42.6161C12.0005 42.5688 11.998 42.5177 11.9931 42.4691C11.9881 42.4317 11.9781 42.3943 11.9781 42.3569V15.5801L6.18848 12.2446L1.99677 9.83281ZM12.9777 2.36177L2.99764 8.10652L12.9752 13.8513L22.9541 8.10527L12.9752 2.36177H12.9777ZM18.1678 38.2138L23.9574 34.8809V9.83281L19.7657 12.2459L13.9749 15.5801V40.6281L18.1678 38.2138ZM48.9133 9.14105L38.9344 14.8858L48.9133 20.6305L58.8909 14.8846L48.9133 9.14105ZM47.9149 22.3593L42.124 19.0252L37.9323 16.6121V27.9844L43.7219 31.3174L47.9149 33.7317V22.3593ZM24.9533 47.987L39.59 39.631L46.9065 35.4555L36.9352 29.7145L25.4544 36.3242L14.9907 42.3482L24.9533 47.987Z"/>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold bg-gradient-to-r from-gray-900 to-gray-600 dark:from-white dark:to-gray-300 bg-clip-text text-transparent">
                                    ECORDERSYSPH
                                </h1>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Intelligent Ordering System</p>
                            </div>
                        </div>

                        <!-- Navigation -->
                        <nav v-if="canLogin" class="flex items-center space-x-4">
                            <Link
                                v-if="$page.props.auth.user"
                                :href="route('dashboard')"
                                class="px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl font-medium hover:from-blue-700 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl"
                            >
                                Dashboard
                            </Link>

                            <template v-else>
                                <Link
                                    :href="route('login')"
                                    class="px-6 py-3 text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors duration-300 font-medium"
                                >
                                    Log in
                                </Link>
                            </template>
                        </nav>
                    </div>
                </div>
            </header>

            <!-- Hero Section -->
            <div class="relative z-10 flex-1 flex items-center justify-center px-6 py-12">
                <div class="max-w-7xl mx-auto w-full">
                    <!-- Main Content -->
                    <div class="text-center mb-16">
                        <h2 class="text-5xl lg:text-7xl font-bold mb-6">
                            <span class="bg-gradient-to-r from-blue-600 via-purple-600 to-emerald-600 bg-clip-text text-transparent">
                                Streamline
                            </span>
                            <br>
                            <span class="text-gray-900 dark:text-white">Your Orders</span>
                        </h2>
                        <p class="text-xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto leading-relaxed">
                            Experience the future of order management with our intelligent system designed for efficiency, insight, and seamless operations.
                        </p>
                    </div>

                    <!-- Feature Cards -->
                    <div class="grid lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
                        <!-- Analytics Card -->
                        <div class="group relative">
                            <div class="absolute -inset-1 bg-gradient-to-r from-blue-600 to-purple-600 rounded-3xl blur opacity-25 group-hover:opacity-40 transition duration-1000"></div>
                            <div class="relative bg-white/80 dark:bg-gray-900/80 backdrop-blur-xl rounded-3xl p-8 border border-white/20 dark:border-gray-700/50 hover:border-white/40 dark:hover:border-gray-600/50 transition-all duration-500 transform hover:scale-[1.02]">
                                <div class="flex items-center justify-center w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl mb-6 group-hover:scale-110 transition-transform duration-300">
                                    <iframe 
                                        src="https://lottie.host/embed/9abdaf47-7a95-4ab1-a537-cb1fdd508e04/5tEnRaFoYO.json" 
                                        class="w-10 h-10 pointer-events-none"
                                    ></iframe>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                                    Smart Analytics
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                                    Gain deep insights into your ordering patterns with real-time analytics and comprehensive reporting tools that drive informed business decisions.
                                </p>
                            </div>
                        </div>

                        <!-- Efficiency Card -->
                        <div class="group relative">
                            <div class="absolute -inset-1 bg-gradient-to-r from-purple-600 to-emerald-600 rounded-3xl blur opacity-25 group-hover:opacity-40 transition duration-1000"></div>
                            <div class="relative bg-white/80 dark:bg-gray-900/80 backdrop-blur-xl rounded-3xl p-8 border border-white/20 dark:border-gray-700/50 hover:border-white/40 dark:hover:border-gray-600/50 transition-all duration-500 transform hover:scale-[1.02]">
                                <div class="flex items-center justify-center w-16 h-16 bg-gradient-to-r from-purple-500 to-emerald-600 rounded-2xl mb-6 group-hover:scale-110 transition-transform duration-300">
                                    <iframe 
                                        src="https://lottie.host/embed/43c1b491-658d-43f3-8dd5-854446819251/2igOT6RrNk.json" 
                                        class="w-10 h-10 pointer-events-none"
                                    ></iframe>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                                    Empowering Insights
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                                    Centralized data management facilitates better decision-making through real-time access to ordering data, empowering your team with valuable insights.
                                </p>
                            </div>
                        </div>

                        <!-- Performance Card -->
                        <div class="group relative">
                            <div class="absolute -inset-1 bg-gradient-to-r from-emerald-600 to-blue-600 rounded-3xl blur opacity-25 group-hover:opacity-40 transition duration-1000"></div>
                            <div class="relative bg-white/80 dark:bg-gray-900/80 backdrop-blur-xl rounded-3xl p-8 border border-white/20 dark:border-gray-700/50 hover:border-white/40 dark:hover:border-gray-600/50 transition-all duration-500 transform hover:scale-[1.02]">
                                <div class="flex items-center justify-center w-16 h-16 bg-gradient-to-r from-emerald-500 to-blue-600 rounded-2xl mb-6 group-hover:scale-110 transition-transform duration-300">
                                    <iframe 
                                        src="https://lottie.host/embed/95496b19-c361-4dc6-85bb-6577ca0898d9/FdXQ7itP0E.json" 
                                        class="w-10 h-10 pointer-events-none"
                                    ></iframe>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                                    Maximizing Efficiency
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                                    Streamline internal processes and enhance your ability to deliver products faster, more reliably, and at lower costs than competitors.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <footer class="relative z-10 py-8 text-center">
                <div class="max-w-7xl mx-auto px-6">
                    <div class="bg-white/50 dark:bg-gray-900/50 backdrop-blur-xl rounded-2xl p-6 border border-white/20 dark:border-gray-700/50">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                            Powered by Laravel v{{ laravelVersion }} (PHP v{{ phpVersion }})
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-500">
                            Designed with ❤️ by MRPA
                        </p>
                    </div>
                </div>
            </footer>
        </div>
    </div>
</template>

<style scoped>
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

.animate-float {
    animation: float 6s ease-in-out infinite;
}
</style>