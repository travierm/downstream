import media from '../store/modules/media';
import * as types from '../store/mutation-types';

describe('Media Mutations', () => {
	const mutations = media.mutations;
	it('updateCurrent', () => {
		// mock state
		const state = { 
			media: {
				current: false
			} 
		}

		// apply mutation
		mutations[types.UPDATE_CURRENT_VIDEO](state, "sdf2w323rf");
		// assert result
		expect(state.current).toBe("sdf2w323rf");
	})
})