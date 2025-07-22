/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [ 
    "./**/*.php",    
    "./auth/*.php",
    "./pages/*.php",
    "./dashboard/*.php",
    "./includes/*.php"
  ],
  theme: {
    extend: {
      fontFamily: {
        inter: ['Inter', 'sans-serif'],
      },
      colors : {
        "primary" : "#FFA300", //#FFA300
        'background-light': '#FFFFFF', //dcdcdc
        'background-dark': '#00061B', //#00061B
        'dark-text': '#FFFFFF',
        'light-text': '#000000'
      },
      backgroundImage: {
        'hero-pattern-index-page': "url('http://localhost/tourism%20agency/assets/giphy1.gif')",
        'hero-pattern-agencies': "url('http://localhost/tourism%20agency/assets/agencies.jpg')",
        'hero-pattern-locations': "url('http://localhost/tourism%20agency/assets/Nature.jpg')",
        'hero-pattern-index-circuits': "url('http://localhost/tourism%20agency/assets/giphy(3).gif')",
        'hero-pattern-index-locations': "url('http://localhost/tourism%20agency/assets/giphy(1).gif')",
        'hero-pattern-index-acc': "url('http://localhost/tourism%20agency/assets/acc.gif')",
        'hero-pattern-customers': "url('http://localhost/tourism%20agency/assets/customers.png')",
        'hero-pattern-conversations': "url('http://localhost/tourism%20agency/assets/Hero_heading.png')",
      }
    },
  },
  plugins: [],
}

