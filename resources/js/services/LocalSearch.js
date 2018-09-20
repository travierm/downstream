import elasticlunr from 'elasticlunr';

export default class LocalSearch {
	constructor()
	{

	}

	createIndex(cb) {
		return elasticlunr(cb);
	}
}