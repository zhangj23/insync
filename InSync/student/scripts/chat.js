const firebaseConfig = {
  apiKey: "AIzaSyCphEVQpitvS8ly-v5of63gXDZZUdzAhgs",
  authDomain: "realtimechat-23228.firebaseapp.com",
  projectId: "realtimechat-23228",
  storageBucket: "realtimechat-23228.appspot.com",
  messagingSenderId: "172796384578",
  appId: "1:172796384578:web:acbb5ece74ef8ffe5624db",
  measurementId: "G-0348WBKW44"
};

firebase.initializeApp(firebaseConfig);

// Initialize Firestore
const db = firebase.firestore();

// Get current user ID from data attribute
const currentUser = document.querySelector(".sections").getAttribute("data-user1");
let otherUserId = null;
let otherUserName = null;
let messagesRef = null;
let selectedChatType = null;
let selectedChatId = null;
let unsubscribe = null;

// Function to get chat document ID
function getChatId(user1Id, user2Id) {
  return [user1Id, user2Id].sort().join("_");
}


// Function to fetch and display the other user's name
function fetchOtherUserName(userId, type) {
  console.log("Attempting to fetch user with ID:", userId);

  fetch(`./get_username.php?id=${userId}`)
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        otherUserName = data.username;
        console.log("Found name:", otherUserName);
        // Refresh messages to show new username
        fetchMessages();
      } else {
        otherUserName = data.username || "User " + userId;
        console.log("Using default name:", otherUserName);
      }
    })
    .catch((error) => {
      console.error("Error fetching user name:", error);
      otherUserName = "User " + userId;
    });
}

// Function to fetch and display messages
function fetchMessages() {

  if (unsubscribe) {
    unsubscribe();
    unsubscribe = null;
  }

  const chatId = getChatId(currentUser, otherUserId);
  const chatRef = db.collection("chats").doc(chatId);
  messagesRef = chatRef.collection("messages").orderBy("timestamp");
  const messageList = document.getElementById("messages");
  messageList.innerHTML = "";

  unsubscribe = chatRef.collection("messages")
    .orderBy("timestamp")
    .onSnapshot((snapshot) => {
      messageList.innerHTML = "";
      if (snapshot.empty) {
        // Show the "start a chat" message if no messages are found
        const startChatMessage = document.createElement("li");
        startChatMessage.classList.add("start-chat-message");
        startChatMessage.textContent = "Start a chat with " + otherUserName;
        messageList.appendChild(startChatMessage);
      } else {
        snapshot.forEach((doc) => {
          const message = doc.data();
          const messageElement = document.createElement("li");
          const isCurrentUser = message.senderId === currentUser;
          // Handle timestamp
          messageElement.innerHTML = `
                <strong>${isCurrentUser ? "You" : otherUserName}:</strong> 
                ${message.text} 
                <span class="timestamp">
                    ${message.timestamp ? new Date(message.timestamp.toDate()).toLocaleString([], { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }) : "Invalid Date"}
                </span>
            `;
          messageList.appendChild(messageElement);
        });
      }
    });
}

// Add event listeners to user buttons
document.querySelectorAll(".user-btn").forEach(button => {
  button.addEventListener("click", () => {
    otherUserId = button.getAttribute("data-user-id");
    console.log("Selected User ID:", otherUserId);
    fetchOtherUserName(otherUserId);
    fetchMessages(); // Fetch messages for the selected chat
  });
});



// GROUP MESSAGES

// Add event listeners to group buttons
document.querySelectorAll(".group-btn").forEach(button => {
  button.addEventListener("click", () => {
    selectedChatType = 'group';
    selectedChatId = button.getAttribute("data-group-id");
    //fetchChatName(selectedChatId, 'group');
    fetchGroupMessages();
  });
});

// Function to fetch and display group messages
function fetchGroupMessages() {

  if (unsubscribe) {
    unsubscribe();
    unsubscribe = null;
  }

  const messageList = document.getElementById("messages");
  messageList.innerHTML = ""; // Clear previous messages

  const groupChatRef = db.collection("groups").doc(selectedChatId);
  messagesRef = groupChatRef.collection("messages").orderBy("timestamp");

  // messagesRef.onSnapshot((snapshot) => {
  unsubscribe = groupChatRef.collection("messages")
    .orderBy("timestamp")
    .onSnapshot((snapshot) => {
      messageList.innerHTML = "";
      if (snapshot.empty) {
        const startChatMessage = document.createElement("li");
        startChatMessage.classList.add("start-chat-message");
        startChatMessage.textContent = `Start a chat with Group ${selectedChatId}`;
        messageList.appendChild(startChatMessage);
      } else {
        snapshot.forEach((doc) => {
          const message = doc.data();
          const messageElement = document.createElement("li");
          const isCurrentUser = message.senderId === currentUser;
          // Handle timestamp
          const timestamp = message.timestamp
          ? new Date(message.timestamp.toDate()).toLocaleString([], { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' })
          : "Invalid Date";
      
      messageElement.innerHTML = `
          <strong>${isCurrentUser ? "You" : message.senderName}:</strong> 
          ${message.text} 
          <span class="timestamp">${timestamp}</span>
      `;
          messageList.appendChild(messageElement);
        });
      }
    });
}

// Function to send a message to the group chat
function sendGroupMessage(message) {
  const groupChatRef = db.collection("groups").doc(selectedChatId);
  const messagesRef = groupChatRef.collection("messages");

  fetch(`./get_username.php?id=${currentUser}`)
    .then(response => response.json())
    .then(data => {
      const senderName = data.success ? data.username : "User " + currentUser;

      messagesRef.add({
        text: message,
        senderId: currentUser,
        senderName: senderName,
        timestamp: firebase.firestore.FieldValue.serverTimestamp()
      })
        .then(() => {
          console.log("Message sent successfully");
          // Clear the input field
          document.getElementById("chat-txt").value = "";
        })
        .catch((error) => {
          console.error("Error sending message: ", error);
        });
    })
    .catch((error) => {
      console.error("Error fetching sender name:", error);
    });
}



// Send message function for group chats
document.getElementById("send-message").addEventListener("submit", (e) => {
  e.preventDefault();
  const messageInput = document.getElementById("chat-txt");
  const message = messageInput.value;
  if (message.trim() === "") return;

  let messagesRef;

  if (selectedChatType === 'group') {
    // For group chats
    const groupChatRef = db.collection("groups").doc(selectedChatId);
    messagesRef = groupChatRef.collection("messages");
  } else {
    // For direct chats between two users
    const chatId = getChatId(currentUser, otherUserId);
    const chatRef = db.collection("chats").doc(chatId);
    messagesRef = chatRef.collection("messages");
  }

  // Fetch username and send the message
  fetch(`./get_username.php?id=${currentUser}`)
    .then(response => response.json())
    .then(data => {
      const senderName = data.success ? data.username : "User " + currentUser;

      messagesRef.add({
        text: message,
        senderId: currentUser,
        senderName: senderName,
        timestamp: firebase.firestore.FieldValue.serverTimestamp()
      })
        .then(() => {
          console.log("Message sent successfully");
          messageInput.value = ""; // Clear the input field
        })
        .catch((error) => {
          console.error("Error sending message: ", error);
        });
    })
    .catch((error) => {
      console.error("Error fetching sender name:", error);
    });
});
