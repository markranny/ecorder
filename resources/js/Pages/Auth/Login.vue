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
    <!-- <Head title="Log in" /> -->

    <Head title="Log in">
        <meta name="theme-color" content="#000000" />
        <link rel="manifest" href="/manifest.json" />
        <link rel="apple-touch-icon" href="/images/icons/icon-192x192.png">
        <meta name="apple-mobile-web-app-status-bar" content="#000000" />
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="mobile-web-app-capable" content="yes" />
    </Head>

    <header class="bg-navy shadow-md fixed top-0 left-0 right-0 z-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">

            <!-- Centered Logo -->
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <a href="/" class="text-white text-lg font-bold">
                        ECORDERSYSPH
                    </a>
                </div>
            </div>

            <!-- Navigation Links (if needed) -->
            <!-- <nav class="hidden md:block">
                <ul class="flex space-x-4">
                    <li><a href="#" class="text-gray-100 hover:text-gray-800">Home</a></li>
                    <li><a href="#" class="text-gray-100 hover:text-gray-800">About</a></li>
                    <li><a href="#" class="text-gray-100 hover:text-gray-800">Services</a></li>
                    <li><a href="#" class="text-gray-100 hover:text-gray-800">Contact</a></li>
                </ul>
            </nav> -->

        </div>
    </div>
</header>


    <!-- @laravelPWA -->

    <!-- <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                    autofocus
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />
                <TextInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="current-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="block mt-4">
                <label class="flex items-center">
                    <Checkbox v-model:checked="form.remember" name="remember" />
                    <span class="ms-2 text-sm text-gray-600">Remember me</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link v-if="canResetPassword" :href="route('password.request')" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Forgot your password?
                </Link>

                <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Log in
                </PrimaryButton>
            </div>
        </form>
    </AuthenticationCard> -->

    <!-- This is an example component -->
<div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
</div>
 <div class="font-sans">
            <!-- <div class="relative min-h-screen flex flex-col sm:justify-center items-center bg-gray-100 mt-40"> -->
                <div class="justify-center items-center bg-gray-100 md:relative min-h-screen flex flex-col bg-gray-100 mt-0">
                <div class="relative sm:max-w-sm w-full ">
                    <div class="card bg-navy shadow-lg  w-full h-full rounded-3xl absolute  transform -rotate-6"></div>
                    <div class="card bg-blue-400 shadow-lg  w-full h-full rounded-3xl absolute  transform rotate-6"></div>
                    <div class="relative w-full rounded-3xl  px-6 py-4 bg-gray-100 shadow-md">
                        <label for="" class="block mt-3 text-sm text-gray-700 text-center font-semibold">
                            Login
                        </label>
                        <form @submit.prevent="submit">
                                           
                            <!-- <div>
                                <input type="email" placeholder="Email" class="mt-1 block w-full border-none bg-gray-100 h-11 rounded-xl shadow-lg hover:bg-blue-100 focus:bg-blue-100 focus:ring-0">
                            </div> -->

                            <div>
                                <TextInput
                                id="email"
                                v-model="form.email"
                                type="email"
                                placeholder="Email"
                                class="mt-1 block w-full border-none !bg-gray-100 !text-navy h-11 rounded-xl shadow-lg hover:bg-blue-100 focus:bg-blue-100 focus:ring-0"
                                required
                                autofocus
                                autocomplete="username"
                                />
                                <InputError class="mt-2" :message="form.errors.email" />
                            </div>
                            
                            <div class="mt-7">                
                                <!-- <input type="password" placeholder="Password" class="mt-1 block w-full border-none bg-gray-100 h-11 rounded-xl shadow-lg hover:bg-blue-100 focus:bg-blue-100 focus:ring-0">                            -->
                                <!-- <InputLabel for="password" value="Password" /> -->
                                <TextInput
                                    id="password"
                                    v-model="form.password"
                                    type="password"
                                    placeholder="Password"
                                    class="mt-1 block w-full border-none bg-gray-100 h-11 rounded-xl shadow-lg hover:bg-blue-100 focus:bg-blue-100 focus:ring-0"
                                    required
                                    autocomplete="current-password"
                                />
                                <InputError class="mt-2" :message="form.errors.password" />
                            </div>

                            <div class="mt-7 flex">
                                <label for="remember_me" class="inline-flex items-center w-full cursor-pointer">
                                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="remember">
                                    <span class="ml-2 text-sm text-gray-600">
                                        Remember me
                                    </span>
                                </label>
                
                               <div class="w-full text-right">     
                                    <a class="underline text-sm text-gray-600 hover:text-gray-900" href="#">
                                        Forgot your password?
                                    </a>                                  
                               </div>
                            </div>
                
                            <div class="mt-7">
                                <button 
                                class="bg-blue-500 w-full py-3 rounded-xl text-white shadow-xl hover:shadow-inner focus:outline-none transition duration-500 ease-in-out  transform hover:-translate-x hover:scale-105"
                                :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                                >
                                    
                                Login
                                </button>
                            </div>
                
                            <!-- <div class="flex mt-7 items-center text-center">
                                <hr class="border-gray-300 border-1 w-full rounded-md">
                                <label class="block font-medium text-sm text-gray-600 w-full">
                                    Accede con
                                </label>
                                <hr class="border-gray-300 border-1 w-full rounded-md">
                            </div> -->
                
                            <!-- <div class="flex mt-7 justify-center w-full">
                                <button class="mr-5 bg-blue-500 border-none px-4 py-2 rounded-xl cursor-pointer text-white shadow-xl hover:shadow-inner transition duration-500 ease-in-out  transform hover:-translate-x hover:scale-105">
                                    
                                    Facebook
                                </button>
                
                                <button class="bg-red-500 border-none px-4 py-2 rounded-xl cursor-pointer text-white shadow-xl hover:shadow-inner transition duration-500 ease-in-out  transform hover:-translate-x hover:scale-105">
                                    
                                    Google
                                </button>
                            </div> -->
                
                             <!-- <div class="mt-7">
                                <div class="flex justify-center items-center">
                                    <label class="mr-2" >¿Eres nuevo?</label>
                                    <a href="#" class=" text-blue-500 transition duration-500 ease-in-out  transform hover:-translate-x hover:scale-105">
                                        Crea una cuenta
                                    </a>
                                </div>
                            </div> -->
                        </form>
                    </div>
                </div>
            </div>
            
        </div>

        <footer class="bg-navy text-gray-300 py-4 fixed bottom-0 w-full">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-center">
                <p class="text-sm">&copy; 2024 ELjin Corp. | MRPA</p>
            </div>
        </footer>


</template>
