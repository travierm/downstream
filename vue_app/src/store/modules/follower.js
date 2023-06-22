import {
  fetchFollowage,
  follow,
  unFollow,
} from '@/services/api/FollowerService'

export const namespaced = true
export const state = {
  following: undefined,
  followers: undefined,
}

export const mutations = {
  SET_FOLLOWERS(state, followers) {
    state.followers = followers || []
  },
  SET_FOLLOWING(state, following) {
    state.following = following || []
  },
  FOLLOW(state, following) {
    state.following = [...state.following, following]
  },
  UNFOLLOW(state, followingId) {
    state.following = state.following.filter((i) => i.id != followingId)
  },
}

export const getters = {
  isFollowing(state) {
    return (followIdOrHash) => {
      if (state.following === undefined) {
        return state.following
      }

      return !!state.following.find(
        (i) => i.id == followIdOrHash || i.hash == followIdOrHash
      )
    }
  },
}

export const actions = {
  async getFollowage(context) {
    if (!context.rootState.auth.token) {
      return
    }

    const result = await fetchFollowage()

    context.commit('SET_FOLLOWERS', result.data.followers)
    context.commit('SET_FOLLOWING', result.data.following)
  },
  async follow(context, followId) {
    if (!context.rootState.auth.token) {
      return
    }

    const result = await follow(followId)
    context.commit('FOLLOW', result.data.followedUser)
  },
  async unfollow(context, followId) {
    if (!context.rootState.auth.token) {
      return
    }

    await unFollow(followId)
    context.commit('UNFOLLOW', followId)
  },
}
