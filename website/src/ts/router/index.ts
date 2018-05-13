import Vue from 'vue'
import Router from 'vue-router'
import About from '../components/About.vue'
import Article from '../components/Article.vue'
import Blog from '../components/Blog.vue'
import Contact from '../components/Contact.vue'
import Courtesy from '../components/Courtesy.vue'
import Delegation from '../components/Delegation.vue'
import FAQ from '../components/FAQ.vue'
import Notices from '../components/Notices.vue'
import Trail from '../components/Trail.vue'

Vue.use(Router)

export default new Router({
	routes: [
		{
			path: '/',
			name: 'Home',
		},
		{
			path: '/article/:author/:permlink',
			name: 'Article',
			props: true,
			component: Article
		},
		{
			path: '/about',
			name: 'About',
			component: About
		},
		{
			path: '/blog',
			name: 'Blog',
			component: Blog
		},
		{
			path: '/contact',
			name: 'Contact',
			component: Contact
		},
		{
			path: '/courtesy',
			name: 'Courtesy',
			component: Courtesy
		},
		{
			path: '/delegation',
			name: 'Delegation',
			component: Delegation
		},
		{
			path: '/faq',
			name: 'FAQ',
			component: FAQ
		},
		{
			path: '/notices',
			name: 'Notices',
			component: Notices
		},
		{
			path: '/trail',
			name: 'Trail',
			component: Trail
		}
	]
})