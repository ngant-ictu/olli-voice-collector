import * as firebase from 'firebase/app'
import 'firebase/auth';

const config = {
  apiKey: "AIzaSyC4SQmqskZaW1P1QvWZWGUEZX9He6GWHAw",
  authDomain: "olli-event-app.firebaseapp.com",
  databaseURL: "https://olli-event-app.firebaseio.com",
  projectId: "olli-event-app",
  storageBucket: "olli-event-app.appspot.com",
  messagingSenderId: "944384975408"
}

export default !firebase.apps.length ? firebase.initializeApp(config) : firebase.app()
export const Auth = firebase.auth
