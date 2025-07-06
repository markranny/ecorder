<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Log in">
        <meta name="theme-color" content="#000000" />
        <link rel="manifest" href="/manifest.json" />
        <link rel="apple-touch-icon" href="/images/icons/icon-192x192.png">
        <meta name="apple-mobile-web-app-status-bar" content="#000000" />
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="mobile-web-app-capable" content="yes" />
    </Head>

    <!-- Ultra Responsive Header -->
    <header class="bg-gradient-to-r from-blue-900 via-indigo-900 to-purple-900 shadow-2xl fixed top-0 left-0 right-0 z-30 backdrop-blur-md border-b border-blue-800/30">
        <div class="max-w-7xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8">
            <div class="flex justify-between items-center h-12 sm:h-14 md:h-16">
                <!-- Responsive Logo -->
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <a href="/" class="text-white text-sm sm:text-base md:text-lg font-bold tracking-wide hover:text-blue-200 transition-all duration-300 transform hover:scale-105">
                            <span class="hidden sm:inline">ECORDERSYSPH</span>
                            <span class="sm:hidden">EORDER</span>
                        </a>
                    </div>
                </div>
                
                <!-- Responsive Navigation -->
                <div class="flex items-center space-x-2 sm:space-x-4">
                    <Link href="/" class="text-blue-200 hover:text-white text-xs sm:text-sm transition-all duration-300 transform hover:scale-105 px-2 py-1 rounded-lg hover:bg-white/10">
                        <span class="hidden sm:inline">← Back to Home</span>
                        <span class="sm:hidden">← Home</span>
                    </Link>
                </div>
            </div>
        </div>
    </header>

    <!-- Ultra Responsive Background with Advanced Animations -->
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 relative overflow-hidden">
        <!-- Multiple Animated Background Layers -->
        <div class="absolute inset-0">
            <!-- Large floating orbs -->
            <div class="absolute top-1/4 left-1/4 w-24 h-24 sm:w-32 sm:h-32 md:w-48 md:h-48 lg:w-64 lg:h-64 bg-gradient-to-br from-blue-400/30 to-purple-500/30 rounded-full blur-3xl animate-float-slow"></div>
            <div class="absolute bottom-1/3 right-1/4 w-20 h-20 sm:w-28 sm:h-28 md:w-40 md:h-40 lg:w-56 lg:h-56 bg-gradient-to-br from-indigo-400/30 to-pink-500/30 rounded-full blur-3xl animate-float-medium"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-16 h-16 sm:w-24 sm:h-24 md:w-36 md:h-36 lg:w-48 lg:h-48 bg-gradient-to-br from-cyan-400/30 to-blue-600/30 rounded-full blur-3xl animate-float-fast"></div>
            
            <!-- Small particles -->
            <div class="absolute top-20 left-20 w-4 h-4 sm:w-6 sm:h-6 bg-blue-300/50 rounded-full animate-pulse-slow"></div>
            <div class="absolute top-40 right-32 w-3 h-3 sm:w-4 sm:h-4 bg-purple-300/50 rounded-full animate-pulse-medium"></div>
            <div class="absolute bottom-32 left-16 w-5 h-5 sm:w-7 sm:h-7 bg-indigo-300/50 rounded-full animate-pulse-fast"></div>
            <div class="absolute bottom-20 right-20 w-2 h-2 sm:w-3 sm:h-3 bg-cyan-300/50 rounded-full animate-bounce-slow"></div>
            
            <!-- Geometric shapes -->
            <div class="absolute top-1/3 right-1/3 w-8 h-8 sm:w-12 sm:h-12 bg-gradient-to-r from-blue-400/20 to-purple-400/20 transform rotate-45 animate-spin-slow"></div>
            <div class="absolute bottom-1/4 left-1/3 w-6 h-6 sm:w-10 sm:h-10 bg-gradient-to-r from-indigo-400/20 to-pink-400/20 transform rotate-12 animate-wiggle"></div>
        </div>

        <!-- Animated grid overlay -->
        <div class="absolute inset-0 bg-grid-pattern opacity-5 animate-grid-move"></div>

        <!-- Status Message with Ultra Responsiveness -->
        <div v-if="status" class="fixed top-14 sm:top-16 md:top-20 left-1/2 transform -translate-x-1/2 z-40 px-3 sm:px-4 w-full max-w-xs sm:max-w-sm md:max-w-md">
            <div class="bg-green-50/95 border border-green-200 rounded-lg sm:rounded-xl p-3 sm:p-4 shadow-lg backdrop-blur-md animate-slide-down">
                <p class="text-xs sm:text-sm text-green-700 text-center font-medium">{{ status }}</p>
            </div>
        </div>

        <!-- Ultra Responsive Main Container -->
        <div class="font-sans relative z-20">
            <div class="min-h-screen flex flex-col justify-center items-center px-3 sm:px-4 md:px-6 pt-14 sm:pt-16 md:pt-20 pb-14 sm:pb-16">
                <div class="relative w-full max-w-xs sm:max-w-sm md:max-w-md lg:max-w-lg">
                    <!-- Ultra Enhanced Layered Cards with Animations -->
                    <div class="card bg-gradient-to-br from-blue-800 via-blue-900 to-indigo-900 shadow-2xl w-full h-full rounded-xl sm:rounded-2xl md:rounded-3xl absolute transform -rotate-2 sm:-rotate-3 opacity-70 animate-card-float-1"></div>
                    <div class="card bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-600 shadow-xl w-full h-full rounded-xl sm:rounded-2xl md:rounded-3xl absolute transform rotate-2 sm:rotate-3 opacity-80 animate-card-float-2"></div>
                    
                    <!-- Ultra Responsive Main Login Card -->
                    <div class="relative w-full rounded-xl sm:rounded-2xl md:rounded-3xl px-4 sm:px-6 md:px-8 py-6 sm:py-7 md:py-8 bg-white/95 backdrop-blur-xl shadow-2xl border border-white/50 animate-fade-in-up">
                        <!-- Ultra Responsive Header -->
                        <div class="text-center mb-4 sm:mb-6 md:mb-8">
                            <div class="mb-3 sm:mb-4">
                                <div class="w-12 h-12 sm:w-16 sm:h-16 md:w-20 md:h-20 mx-auto bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center shadow-lg animate-pulse-gentle">
                                    <svg class="w-6 h-6 sm:w-8 sm:h-8 md:w-10 md:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                            </div>
                            <h2 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-800 mb-1 sm:mb-2">Welcome Back</h2>
                            <p class="text-xs sm:text-sm text-gray-600">Sign in to your ordering account</p>
                        </div>

                        <form @submit.prevent="submit" class="space-y-4 sm:space-y-5 md:space-y-6">
                            <!-- Ultra Responsive Email Input -->
                            <div class="space-y-1 sm:space-y-2">
                                <label for="email" class="block text-xs sm:text-sm font-medium text-gray-700">Email Address</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none transition-colors duration-300 group-focus-within:text-blue-500">
                                        <svg class="h-4 w-4 sm:h-5 sm:w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                        </svg>
                                    </div>
                                    <TextInput
                                        id="email"
                                        v-model="form.email"
                                        type="email"
                                        placeholder="Enter your email"
                                        class="pl-9 sm:pl-10 pr-3 sm:pr-4 py-2.5 sm:py-3 w-full border border-gray-200 rounded-lg sm:rounded-xl bg-gray-50/50 backdrop-blur-sm text-gray-800 text-sm sm:text-base placeholder-gray-500 hover:bg-white hover:border-blue-300 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-300 transform focus:scale-[1.02]"
                                        required
                                        autofocus
                                        autocomplete="username"
                                    />
                                </div>
                                <InputError class="mt-1 text-xs sm:text-sm text-red-600" :message="form.errors.email" />
                            </div>
                            
                            <!-- Ultra Responsive Password Input -->
                            <div class="space-y-1 sm:space-y-2">
                                <label for="password" class="block text-xs sm:text-sm font-medium text-gray-700">Password</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none transition-colors duration-300 group-focus-within:text-blue-500">
                                        <svg class="h-4 w-4 sm:h-5 sm:w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    </div>
                                    <TextInput
                                        id="password"
                                        v-model="form.password"
                                        type="password"
                                        placeholder="Enter your password"
                                        class="pl-9 sm:pl-10 pr-3 sm:pr-4 py-2.5 sm:py-3 w-full border border-gray-200 rounded-lg sm:rounded-xl bg-gray-50/50 backdrop-blur-sm text-gray-800 text-sm sm:text-base placeholder-gray-500 hover:bg-white hover:border-blue-300 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-300 transform focus:scale-[1.02]"
                                        required
                                        autocomplete="current-password"
                                    />
                                </div>
                                <InputError class="mt-1 text-xs sm:text-sm text-red-600" :message="form.errors.password" />
                            </div>

                            <!-- Ultra Responsive Remember Me & Forgot Password -->
                            <div class="flex flex-col space-y-3 sm:space-y-0 sm:flex-row sm:items-center sm:justify-between">
                                <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                                    <input 
                                        id="remember_me" 
                                        v-model="form.remember"
                                        type="checkbox" 
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 transform hover:scale-110" 
                                        name="remember"
                                    >
                                    <span class="ml-2 text-xs sm:text-sm text-gray-600 select-none group-hover:text-gray-800 transition-colors duration-200">
                                        Remember me
                                    </span>
                                </label>
                
                                <div class="text-left sm:text-right">     
                                    <Link 
                                        v-if="canResetPassword" 
                                        :href="route('password.request')" 
                                        class="underline text-xs sm:text-sm text-blue-600 hover:text-blue-800 transition-all duration-300 font-medium transform hover:scale-105 inline-block"
                                    >
                                        Forgot password?
                                    </Link>
                                    <a 
                                        v-else
                                        class="underline text-xs sm:text-sm text-blue-600 hover:text-blue-800 transition-all duration-300 font-medium cursor-pointer transform hover:scale-105 inline-block"
                                        @click="alert('Password reset feature not available')"
                                    >
                                        Forgot password?
                                    </a>                                  
                                </div>
                            </div>
                
                            <!-- Ultra Responsive Login Button -->
                            <div class="pt-2 sm:pt-4">
                                <button 
                                    type="submit"
                                    class="w-full py-2.5 sm:py-3 md:py-3.5 px-4 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 text-white font-semibold text-sm sm:text-base rounded-lg sm:rounded-xl shadow-lg hover:from-blue-700 hover:via-indigo-700 hover:to-purple-700 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none animate-gradient-x"
                                    :class="{ 'opacity-50 cursor-not-allowed': form.processing }" 
                                    :disabled="form.processing"
                                >
                                    <span v-if="form.processing" class="flex items-center justify-center">
                                        <svg class="animate-spin -ml-1 mr-2 sm:mr-3 h-4 w-4 sm:h-5 sm:w-5 text-white" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span class="text-xs sm:text-sm">Signing in...</span>
                                    </span>
                                    <span v-else class="flex items-center justify-center">
                                        <svg class="mr-2 h-4 w-4 sm:h-5 sm:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                        </svg>
                                        Sign In
                                    </span>
                                </button>
                            </div>
                        </form>

                        <!-- Ultra Responsive Additional Options -->
                        <div class="mt-4 sm:mt-6 text-center space-y-2 sm:space-y-3">
                            <div class="flex items-center justify-center">
                                <div class="border-t border-gray-200 flex-1"></div>
                                <span class="px-3 text-xs text-gray-500 bg-white">or</span>
                                <div class="border-t border-gray-200 flex-1"></div>
                            </div>
                            <p class="text-xs sm:text-sm text-gray-600">
                                Need help? 
                                <a href="#" class="text-blue-600 hover:text-blue-800 font-medium transition-all duration-300 transform hover:scale-105 inline-block">
                                    Contact support
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ultra Responsive Footer -->
        <footer class="bg-gradient-to-r from-blue-900 via-indigo-900 to-purple-900 text-gray-300 py-2 sm:py-3 md:py-4 fixed bottom-0 w-full backdrop-blur-md border-t border-blue-800/30 z-30">
            <div class="max-w-7xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8 flex flex-col sm:flex-row justify-center items-center space-y-1 sm:space-y-0 sm:space-x-4">
                <p class="text-xs sm:text-sm text-center">&copy; 2024 ELjin Corp. | MRPA</p>
                <div class="hidden sm:block w-px h-4 bg-gray-600"></div>
                <p class="text-xs text-gray-400 text-center">Secure Ordering System</p>
            </div>
        </footer>
    </div>
</template>

<style scoped>
/* Advanced Animation Keyframes */
@keyframes float-slow {
    0%, 100% { transform: translateY(0px) scale(1) rotate(0deg); }
    25% { transform: translateY(-20px) scale(1.05) rotate(1deg); }
    50% { transform: translateY(-10px) scale(1.1) rotate(0deg); }
    75% { transform: translateY(-30px) scale(1.05) rotate(-1deg); }
}

@keyframes float-medium {
    0%, 100% { transform: translateY(0px) scale(1) rotate(0deg); }
    33% { transform: translateY(-15px) scale(1.03) rotate(2deg); }
    66% { transform: translateY(-25px) scale(1.08) rotate(-1deg); }
}

@keyframes float-fast {
    0%, 100% { transform: translateY(0px) scale(1); }
    50% { transform: translateY(-40px) scale(1.15); }
}

@keyframes pulse-slow {
    0%, 100% { opacity: 0.3; transform: scale(1); }
    50% { opacity: 0.8; transform: scale(1.2); }
}

@keyframes pulse-medium {
    0%, 100% { opacity: 0.4; transform: scale(1); }
    50% { opacity: 0.9; transform: scale(1.3); }
}

@keyframes pulse-fast {
    0%, 100% { opacity: 0.5; transform: scale(1); }
    50% { opacity: 1; transform: scale(1.4); }
}

@keyframes bounce-slow {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

@keyframes spin-slow {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

@keyframes wiggle {
    0%, 100% { transform: rotate(0deg); }
    25% { transform: rotate(3deg); }
    75% { transform: rotate(-3deg); }
}

@keyframes card-float-1 {
    0%, 100% { transform: translateY(0px) rotate(-2deg) scale(1); }
    50% { transform: translateY(-8px) rotate(-3deg) scale(1.02); }
}

@keyframes card-float-2 {
    0%, 100% { transform: translateY(0px) rotate(2deg) scale(1); }
    50% { transform: translateY(-12px) rotate(3deg) scale(1.03); }
}

@keyframes fade-in-up {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slide-down {
    from {
        opacity: 0;
        transform: translateX(-50%) translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
    }
}

@keyframes pulse-gentle {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

@keyframes gradient-x {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

@keyframes grid-move {
    0% { transform: translateX(0) translateY(0); }
    100% { transform: translateX(20px) translateY(20px); }
}

/* Animation Classes */
.animate-float-slow { animation: float-slow 8s ease-in-out infinite; }
.animate-float-medium { animation: float-medium 6s ease-in-out infinite 1s; }
.animate-float-fast { animation: float-fast 4s ease-in-out infinite 2s; }
.animate-pulse-slow { animation: pulse-slow 4s ease-in-out infinite; }
.animate-pulse-medium { animation: pulse-medium 3s ease-in-out infinite 0.5s; }
.animate-pulse-fast { animation: pulse-fast 2s ease-in-out infinite 1s; }
.animate-bounce-slow { animation: bounce-slow 3s ease-in-out infinite; }
.animate-spin-slow { animation: spin-slow 8s linear infinite; }
.animate-wiggle { animation: wiggle 2s ease-in-out infinite; }
.animate-card-float-1 { animation: card-float-1 6s ease-in-out infinite; }
.animate-card-float-2 { animation: card-float-2 6s ease-in-out infinite 0.5s; }
.animate-fade-in-up { animation: fade-in-up 0.8s ease-out; }
.animate-slide-down { animation: slide-down 0.5s ease-out; }
.animate-pulse-gentle { animation: pulse-gentle 3s ease-in-out infinite; }
.animate-gradient-x { 
    background-size: 200% 200%;
    animation: gradient-x 3s ease infinite;
}
.animate-grid-move { animation: grid-move 20s linear infinite; }

/* Background Grid Pattern */
.bg-grid-pattern {
    background-image: 
        linear-gradient(rgba(99, 102, 241, 0.1) 1px, transparent 1px),
        linear-gradient(90deg, rgba(99, 102, 241, 0.1) 1px, transparent 1px);
    background-size: 20px 20px;
}

/* Enhanced input focus styles */
input:focus {
    transform: translateY(-1px) scale(1.02);
}

/* Button hover effect enhancement */
button:hover:not(:disabled) {
    box-shadow: 0 15px 35px -5px rgba(59, 130, 246, 0.4);
}

/* Mobile Performance Optimizations */
@media (max-width: 640px) {
    .animate-float-slow,
    .animate-float-medium,
    .animate-float-fast {
        animation-duration: 12s; /* Slower on mobile */
    }
    
    .animate-card-float-1,
    .animate-card-float-2 {
        animation-duration: 8s; /* Slower card animations */
    }
}

/* Reduced motion for accessibility */
@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .bg-white\/95 {
        background-color: white;
    }
    
    .text-gray-600 {
        color: #000;
    }
    
    .border-gray-200 {
        border-color: #000;
    }
}

/* Dark mode responsive adjustments */
@media (prefers-color-scheme: dark) {
    .bg-white\/95 {
        background-color: rgba(31, 41, 55, 0.95);
    }
    
    .text-gray-800 {
        color: rgb(243, 244, 246);
    }
    
    .text-gray-600 {
        color: rgb(209, 213, 219);
    }
}
</style>