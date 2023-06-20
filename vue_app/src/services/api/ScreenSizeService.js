export function getScreenSizeCategory() {
  const screenWidth = window.innerWidth

  if (screenWidth < 640) {
    return 'sm'
  } else if (screenWidth < 768) {
    return 'md'
  } else if (screenWidth < 1024) {
    return 'lg'
  } else {
    return 'xl'
  }
}

export function getPlayerSizeByCategory(category) {
  if (category === 'sm') {
    return {
      width: 356,
      height: 200,
    }
  } else if (category === 'md') {
    return {
      width: 480,
      height: 270,
    }
  } else if (category === 'lg') {
    return {
      width: 640,
      height: 360,
    }
  } else {
    return {
      width: 1079,
      height: 607,
    }
  }
}
