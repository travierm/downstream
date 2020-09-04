import Model from "objectmodel";

const MediaModel = {
    id: Number,
    user_id: Number,
    meta: JSON,
    created_at: String
};

export default class Media extends Model(MediaModel) {
    
}