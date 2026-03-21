// Import and configure the Firebase SDK
// These scripts are made available when the app is served or deployed on Firebase Hosting
// If you do not serve/host your project using Firebase Hosting see https://firebase.google.com/docs/web/setup
importScripts('https://www.gstatic.com/firebasejs/8.2.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.2.0/firebase-messaging.js');
 
// Your web app's Firebase configuration
var firebaseConfig = {
  //apiKey: "AIzaSyCfGyCTfYKVkPY9Md9KIPkDszKWzFY08eg",
  //authDomain: "myvegiz-82e06.firebaseapp.com",
  //databaseURL: "https://myvegiz-82e06.firebaseio.com",
  //projectId: "myvegiz-82e06",
  //storageBucket: "myvegiz-82e06.appspot.com",
  //messagingSenderId: "923183195433",
  //appId: "1:923183195433:web:552677860bf99989498338",
  //measurementId: "G-BRB8P9M67X"
  apiKey: "AIzaSyBbOLBH2CYPBvtqc6vbvvJjjXOpZS0h4Mk",
  authDomain: "myvegiz-a3615.firebaseapp.com",
  projectId: "myvegiz-a3615",
  storageBucket: "myvegiz-a3615.appspot.com",
  messagingSenderId: "216385826090",
  appId: "1:216385826090:web:d55c6708b5f945d9670253",
  measurementId: "G-R9TPF50PZ3"
};
// Initialize Firebase
firebase.initializeApp(firebaseConfig); 
const messaging = firebase.messaging();

messaging.onBackgroundMessage(function(payload) {
  console.log('[firebase-messaging-sw.js] Received background message ', payload);
  // Customize notification here
  const notificationTitle = payload.notification.title;
  const notificationOptions = {
    body: payload.notification.body,
    icon: '/notify.png',
    sound:'/ringing.mp3'
  };  
  
  self.registration.showNotification(notificationTitle,notificationOptions);
});
 