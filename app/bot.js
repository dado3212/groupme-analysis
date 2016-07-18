import request from 'request';

const token = process.env.GROUPME_TOKEN;

function respond() {
  const msg = JSON.parse(this.req.chunks[0]);
  // const ebaRegex = /^\/ebas$/i;

  // Give blank success response
  this.res.writeHead(200);
  this.res.end();

  if (msg.sender_type !== 'system' && msg.sender_type !== 'bot' && msg.sender_id !== '39434930') {
    directMessage(msg.user_id, msg.text);
  }
}

function directMessage(user, message) {
  request.post({
    headers: { 'content-type': 'application/json' },
    url: `https://api.groupme.com/v3/direct_messages?token=${token}`,
    body: JSON.stringify({
      direct_message: {
        attachments: [],
        recipient_id: user,
        text: message,
      },
    }),
  }, (error, response, body) => {
    // console.log(body);
  });
}

exports.respond = respond;
