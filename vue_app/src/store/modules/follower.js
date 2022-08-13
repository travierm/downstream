import FollowerService from '@/services/api/FollowerService'

export const namespaced = true
export const state = {
  following: [],
  followers: [],
}

export const mutations = {
  SET_FOLLOWERS(state, followers) {
    state.followers = followers
  },
  SET_FOLLOWING(state, followings) {
    state.followings = followings
  },
  FOLLOW(state, following) {
    state.followings = [...state.followings, following]
  },
  UNFOLLOW(state, followingId) {
    state.followings = state.followings.filter((i) => i.id != followingId)
  },
}

export const getters = {
  followers() {
    return state.followers
  },
  following() {
    return state.following
  },
}

export const actions = {
  async getFollowage(context) {
    if (!context.rootState.auth.token) {
      return
    }

    const result = await FollowerService.fetchFollowage()

    context.commit('SET_FOLLOWERS', result.data.followers)
    context.commit('SET_FOLLOWING', result.data.following)
  },
  async follow(context, followId) {
    if (!context.rootState.auth.token) {
      return
    }

    const followed = await FollowerService.follow(followId)
    context.commit('FOLLOW', followed)
  },
  async unFollow(context, followId) {
    if (!context.rootState.auth.token) {
      return
    }

    await FollowerService.unFollow(followId)
    context.commit('UNFOLLOW', followId)
  },
}
