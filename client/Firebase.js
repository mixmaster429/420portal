import firebase from 'firebase/app';
import 'firebase/database';

// const config = {
//     apiKey: "AIzaSyAmgX3-NmiMl9DmuFGkurMDpyqRQo1vttY",
//     authDomain: "portal-c53a5.firebaseapp.com",
//     databaseURL: "https://portal-c53a5.firebaseio.com",
//     projectId: "portal-c53a5",
//     storageBucket: "portal-c53a5.appspot.com",
// };

const config = {
    apiKey: "AIzaSyAT9tFAsh1rw2elJ531ZkOEQtyGjKSS_-g",
    authDomain: "myportal-6ce2f.firebaseapp.com",
    databaseURL: "https://myportal-6ce2f.firebaseio.com",
    projectId: "myportal-6ce2f",
    storageBucket: "myportal-6ce2f.appspot.com",
};

if (!firebase.apps.length) {
    firebase.initializeApp(config);
}

export default firebase;
